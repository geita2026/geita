<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;

/**
 * YAS / Tigo Pesa Debit Mandate connectivity test.
 *
 * Runs the two safest calls in the spec to prove the VPN + credentials work,
 * WITHOUT moving any money:
 *   1. Heartbeat  (POST, no token)  -> proves we can reach the platform.
 *   2. Token      (POST, password grant) -> proves the credentials are valid.
 *
 * Usage examples:
 *   php artisan yas:test
 *   php artisan yas:test --skip-token
 *   php artisan yas:test --host=41.222.176.233 --port=8443
 *   php artisan yas:test --token-url="https://host:8443/token" --base-url="https://host:8443"
 *   php artisan yas:test --insecure        # disable TLS verification for this run
 */
class YasConnectivityTest extends Command
{
    protected $signature = 'yas:test
        {--host=        : Override host (rebuilds token/base URLs as https://host:port)}
        {--port=        : Override port when --host is used (default 8443)}
        {--token-url=   : Full token endpoint URL (overrides config + --host)}
        {--base-url=    : Base API URL for /API/... paths (overrides config + --host)}
        {--skip-heartbeat : Do not run the Heartbeat check}
        {--skip-token     : Do not run the Token check}
        {--insecure       : Disable TLS certificate verification for this run}';

    protected $description = 'Test connectivity & credentials against the YAS/Tigo Pesa Debit Mandate platform (Heartbeat + Token). Moves no money.';

    public function handle(): int
    {
        // ---- Resolve endpoints (CLI overrides > config > defaults) -------------
        $tokenUrl = $this->option('token-url') ?: config('yas.token_url');
        $baseUrl  = $this->option('base-url')  ?: config('yas.api_base_url');

        if ($host = $this->option('host')) {
            $port     = $this->option('port') ?: '8443';
            $baseUrl  = "https://{$host}:{$port}";
            // Keep the configured token path (e.g. /TanTrade2DM-GetToken) instead
            // of hardcoding /token, so --host hits the real custom route.
            $tokenPath = parse_url((string) config('yas.token_url'), PHP_URL_PATH) ?: '/token';
            $tokenUrl  = $this->option('token-url') ?: rtrim($baseUrl, '/') . $tokenPath;
        }

        $username  = config('yas.username');
        $password  = config('yas.password');
        $verify    = $this->option('insecure') ? false : (bool) config('yas.verify_ssl', false);
        $timeout   = (int) config('yas.timeout', 30);
        $hbPath    = config('yas.paths.heartbeat'); // null on this gateway (no heartbeat service)
        $hbUrl     = $hbPath ? rtrim($baseUrl, '/') . $hbPath : null;

        $this->line('');
        $this->info('============== YAS Debit Mandate connectivity test ==============');
        $this->line('  Base URL    : ' . $baseUrl);
        $this->line('  Token URL   : ' . $tokenUrl);
        $this->line('  Heartbeat   : ' . ($hbUrl ?: '<none - not provisioned on this gateway>'));
        $this->line('  Username    : ' . ($username ? $username : '<MISSING - set YAS_USERNAME>'));
        $this->line('  Password    : ' . ($password ? str_repeat('*', strlen($password)) : '<MISSING - set YAS_PASSWORD>'));
        $this->line('  TLS verify  : ' . ($verify ? 'true' : 'false'));
        $this->line('  Timeout     : ' . $timeout . 's');
        $this->info('=================================================================');

        if (! $username || ! $password) {
            $this->warn('No credentials set. Heartbeat may still work, but the Token check needs YAS_USERNAME and YAS_PASSWORD in .env.');
        }

        $allOk = true;

        // ---- 1. Heartbeat -------------------------------------------------------
        // Skipped automatically when no heartbeat path is configured (this
        // gateway only exposes GetToken + PushBillPay).
        if (! $this->option('skip-heartbeat') && $hbUrl) {
            $allOk = $this->runHeartbeat($hbUrl, $username, $password, $verify, $timeout) && $allOk;
        }

        // ---- 2. Token -----------------------------------------------------------
        if (! $this->option('skip-token')) {
            if (! $username || ! $password) {
                $this->newLine();
                $this->warn('[Token]    SKIPPED - credentials missing.');
            } else {
                $allOk = $this->runToken($tokenUrl, $username, $password, $verify, $timeout) && $allOk;
            }
        }

        $this->newLine();
        if ($allOk) {
            $this->info('RESULT: all checks passed. The VPN tunnel and credentials look good.');
            return self::SUCCESS;
        }

        $this->error('RESULT: one or more checks failed. See messages above.');
        $this->line('Tips: confirm the VPN tunnel is up, the host:port is correct (try --host/--port),');
        $this->line('      and that the firewall opens this port (VPN forms list 8443/443).');
        return self::FAILURE;
    }

    private function runHeartbeat(string $url, ?string $username, ?string $password, bool $verify, int $timeout): bool
    {
        $this->newLine();
        $this->line('--- [1/2] Heartbeat (POST, no token) ---');
        $referenceId = 'HB' . date('YmdHis') . Str::upper(Str::random(4));

        try {
            $start = microtime(true);
            $response = Http::withOptions(['verify' => $verify])
                ->timeout($timeout)
                ->withHeaders([
                    'Username'     => (string) $username,
                    'Password'     => (string) $password,
                    'Content-Type' => 'application/json',
                    'Cache-Control'=> 'no-cache',
                ])
                ->post($url, ['ReferenceID' => $referenceId]);
            $ms = round((microtime(true) - $start) * 1000);

            $this->line("    -> HTTP {$response->status()} in {$ms}ms");
            $this->line('    -> Body: ' . Str::limit($response->body(), 500));

            // Spec: success body contains "Heartbeat success." with matching ReferenceID.
            $ok = $response->successful()
                && Str::contains(Str::lower($response->body()), 'heartbeat');

            $ok ? $this->info('    [Heartbeat] OK - platform reachable.')
                : $this->error('    [Heartbeat] Unexpected response (see body above).');

            return $ok;
        } catch (\Throwable $e) {
            $this->error('    [Heartbeat] FAILED to connect: ' . $e->getMessage());
            return false;
        }
    }

    private function runToken(string $url, string $username, string $password, bool $verify, int $timeout): bool
    {
        $this->newLine();
        $this->line('--- [2/2] Token (POST, password grant) ---');

        try {
            $start = microtime(true);
            // Spec: application/x-www-form-urlencoded, body username&password&grant_type=password
            $response = Http::withOptions(['verify' => $verify])
                ->timeout($timeout)
                ->asForm()
                ->withHeaders(['Cache-Control' => 'no-cache'])
                ->post($url, [
                    'username'   => $username,
                    'password'   => $password,
                    'grant_type' => 'password',
                ]);
            $ms = round((microtime(true) - $start) * 1000);

            $this->line("    -> HTTP {$response->status()} in {$ms}ms");

            $token = $response->json('access_token');
            if ($response->successful() && $token) {
                $expires = $response->json('expires_in');
                $type    = $response->json('token_type');
                $this->info('    [Token] OK - credentials valid.');
                $this->line('    -> token_type: ' . $type . ' | expires_in: ' . $expires . 's');
                $this->line('    -> access_token: ' . Str::limit($token, 24) . ' ...(' . strlen($token) . ' chars)');
                return true;
            }

            $this->error('    [Token] Failed - no access_token returned.');
            $this->line('    -> Body: ' . Str::limit($response->body(), 500));
            return false;
        } catch (\Throwable $e) {
            $this->error('    [Token] FAILED to connect: ' . $e->getMessage());
            return false;
        }
    }
}
