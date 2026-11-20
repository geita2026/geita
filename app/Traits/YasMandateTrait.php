<?php

namespace App\Traits;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

/**
 * YAS / Tigo Pesa "Debit Mandate" (USSD PUSH) client.
 *
 * Outbound calls to the YAS Access Gateway:
 *   - getYasToken()   -> POST {token_url}  (form, password grant), cached till expiry
 *   - yasPushBillPay() -> POST {api_base_url}{paths.biller_pay}, bearer auth
 *
 * Connectivity + token were verified working against:
 *   https://accessgwtest.tigo.co.tz:8443/TanTrade2DM-GetToken
 *
 * NOTE: the PushBillPay request body below follows the generic Debit Mandate
 * spec (CustomerMSISDN / BillerMSISDN / Amount / Remarks / ReferenceID). If the
 * "TANTRADE USSD PUSH CREDENTIALS" onboarding doc specifies a different shape
 * for the custom TanTrade2DM-PushBillPay route, adjust $body in yasPushBillPay().
 */
trait YasMandateTrait
{
    /** Cache key for the bearer token. */
    protected string $yasTokenCacheKey = 'yas_dm_access_token';

    /**
     * Obtain a bearer token, cached until shortly before it expires.
     * @param bool $fresh  force a new token, bypassing cache
     * @return string|null  the access token, or null on failure
     */
    public function getYasToken(bool $fresh = false): ?string
    {
        // Cache is an optimisation only - a broken/unwritable cache store must
        // never prevent us from obtaining a token.
        if (! $fresh) {
            try {
                if ($cached = Cache::get($this->yasTokenCacheKey)) {
                    return $cached;
                }
            } catch (\Throwable $e) {
                Log::warning('YAS token cache read failed (ignoring): ' . $e->getMessage());
            }
        }

        $url     = config('yas.token_url');
        $verify  = (bool) config('yas.verify_ssl', false);
        $timeout = (int) config('yas.timeout', 30);

        try {
            $response = Http::withOptions(['verify' => $verify])
                ->timeout($timeout)
                ->asForm()
                ->withHeaders(['Cache-Control' => 'no-cache'])
                ->post($url, [
                    'username'   => config('yas.username'),
                    'password'   => config('yas.password'),
                    'grant_type' => 'password',
                ]);

            $token   = $response->json('access_token');
            $expires = (int) ($response->json('expires_in') ?? 0);

            if ($response->successful() && $token) {
                // Best-effort cache; ignore failures (e.g. unwritable storage).
                try {
                    $ttl = $expires > 120 ? $expires - 120 : 300;
                    Cache::put($this->yasTokenCacheKey, $token, $ttl);
                } catch (\Throwable $e) {
                    Log::warning('YAS token cache write failed (continuing): ' . $e->getMessage());
                }
                return $token;
            }

            Log::warning('YAS getToken failed', [
                'status' => $response->status(),
                'body'   => Str::limit($response->body(), 500),
            ]);
            return null;
        } catch (\Throwable $e) {
            Log::error('YAS getToken exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Trigger a USSD PUSH debit on the customer's YAS wallet.
     *
     * @return array{ok:bool,status:int,body:array,code:?string,description:?string,raw:string}
     */
    public function yasPushBillPay(string $customerMsisdn, float $amount, string $referenceId, ?string $remarks = null): array
    {
        $token = $this->getYasToken();
        if (! $token) {
            return $this->yasResult(false, 0, [], null, 'Could not obtain YAS token', '');
        }

        $base    = rtrim((string) config('yas.api_base_url'), '/');
        $path    = config('yas.paths.biller_pay');
        $url     = $base . $path;
        $verify  = (bool) config('yas.verify_ssl', false);
        $timeout = (int) config('yas.timeout', 30);

        $body = [
            'CustomerMSISDN' => $customerMsisdn,
            'BillerMSISDN'   => config('yas.biller_msisdn'),
            'Amount'         => $amount,
            'Remarks'        => $remarks ?: 'DITF ticket payment',
            'ReferenceID'    => $referenceId,
        ];

        try {
            $response = Http::withOptions(['verify' => $verify])
                ->timeout($timeout)
                ->withHeaders([
                    'Authorization' => 'bearer ' . $token,
                    'Username'      => (string) config('yas.username'),
                    'Password'      => (string) config('yas.password'),
                    'Content-Type'  => 'application/json',
                    'Cache-Control' => 'no-cache',
                ])
                ->post($url, $body);

            $json = $response->json() ?? [];
            // Spec success: ResponseStatus === true (e.g. code BILLER-18-0000-S).
            $ok = $response->successful()
                && filter_var($json['ResponseStatus'] ?? false, FILTER_VALIDATE_BOOLEAN);

            Log::info('YAS PushBillPay', [
                'referenceId' => $referenceId,
                'http'        => $response->status(),
                'code'        => $json['ResponseCode'] ?? null,
                'status'      => $json['ResponseStatus'] ?? null,
            ]);

            return $this->yasResult(
                $ok,
                $response->status(),
                $json,
                $json['ResponseCode'] ?? null,
                $json['ResponseDescription'] ?? null,
                $response->body()
            );
        } catch (\Throwable $e) {
            Log::error('YAS PushBillPay exception: ' . $e->getMessage(), ['referenceId' => $referenceId]);
            return $this->yasResult(false, 0, [], null, $e->getMessage(), '');
        }
    }

    /** Build a unique ReferenceID prefixed with the configured biller code. */
    public function makeYasReference(): string
    {
        $prefix = (string) config('yas.biller_code', 'TANTRADE');
        return $prefix . date('YmdHis') . strtoupper(Str::random(5));
    }

    /**
     * Query YAS for a transaction's real status (TQS — XML over HTTP), used to
     * confirm payment when the success callback doesn't arrive. We look it up by
     * EXTERNALREFID = the ReferenceID we sent in the push.
     *
     * @return array{status:?string,txnid:?string,resultCode:?string,resultDesc:?string,raw:string}
     *         status is TXNSTATUS: 'Posted' (paid), 'Declined' (failed),
     *         'Submitted' (pending), or null/unknown.
     */
    public function yasQueryStatus(string $externalRef, ?string $payerMsisdn = null): array
    {
        $url    = config('yas.tqs_url');
        $msisdn = config('yas.tqs_msisdn') ?: ($payerMsisdn ?: config('yas.biller_msisdn'));
        $pin    = config('yas.tqs_pin');
        // REFERENCEID must be unique and <= 20 chars.
        $queryRef = 'TQS' . strtoupper(Str::random(15));

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'
            . '<COMMAND>'
            . '<TYPE>MTPGGetSODetails</TYPE>'
            . '<REFERENCEID>' . $queryRef . '</REFERENCEID>'
            . '<MSISDN>' . htmlspecialchars((string) $msisdn) . '</MSISDN>'
            . '<PIN>' . htmlspecialchars((string) $pin) . '</PIN>'
            . '<EXTERNALREFID>' . htmlspecialchars($externalRef) . '</EXTERNALREFID>'
            . '</COMMAND>';

        try {
            $response = Http::withOptions(['verify' => (bool) config('yas.verify_ssl', false)])
                ->timeout((int) config('yas.timeout', 30))
                ->withHeaders(['Content-Type' => 'text/xml', 'Connection' => 'keep-alive'])
                ->withBody($xml, 'text/xml')
                ->post($url);

            $body = $response->body();
            $result = [
                'status'     => $this->xmlTag($body, 'TXNSTATUS'),
                'txnid'      => $this->xmlTag($body, 'TXNID'),
                'resultCode' => $this->xmlTag($body, 'RESULTCODE'),
                'resultDesc' => $this->xmlTag($body, 'RESULTDESC'),
                'raw'        => $body,
            ];
            Log::info('YAS TQS query', [
                'externalRef' => $externalRef,
                'http'        => $response->status(),
                'txnStatus'   => $result['status'],
                'resultCode'  => $result['resultCode'],
            ]);
            return $result;
        } catch (\Throwable $e) {
            Log::error('YAS TQS query failed: ' . $e->getMessage(), ['externalRef' => $externalRef]);
            return ['status' => null, 'txnid' => null, 'resultCode' => null, 'resultDesc' => null, 'raw' => ''];
        }
    }

    /** Extract the inner text of the first <tag>…</tag> from an XML string. */
    protected function xmlTag(string $xml, string $tag): ?string
    {
        if (preg_match('/<' . preg_quote($tag, '/') . '>(.*?)<\/' . preg_quote($tag, '/') . '>/is', $xml, $m)) {
            return trim($m[1]);
        }
        return null;
    }

    /** @return array{ok:bool,status:int,body:array,code:?string,description:?string,raw:string} */
    private function yasResult(bool $ok, int $status, array $body, ?string $code, ?string $description, string $raw): array
    {
        return [
            'ok'          => $ok,
            'status'      => $status,
            'body'        => $body,
            'code'        => $code,
            'description' => $description,
            'raw'         => $raw,
        ];
    }
}
