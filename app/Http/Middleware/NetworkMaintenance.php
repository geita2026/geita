<?php

namespace App\Http\Middleware;

use App\Models\setting\DefaultSettings;
use Closure;
use Illuminate\Http\Request;

class NetworkMaintenance
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Always allow the public homepage.
        if (
            $request->is('/') &&
            ($request->isMethod('GET') || $request->isMethod('HEAD'))
        ) {
            return $next($request);
        }

        $clientIp = $request->ip();

        // Allow localhost and local Wi-Fi devices in local environment.
        if (app()->environment('local')) {
            if (in_array($clientIp, ['127.0.0.1', '::1'], true)) {
                return $next($request);
            }

            if ($this->ipMatches($clientIp, '192.168.0.0/22')) {
                return $next($request);
            }
        }

        // Read allowed networks from database.
        $allowed = optional(DefaultSettings::first())->allowedNetworks;

        // Fail closed if allowed networks are not configured.
        if (empty($allowed)) {
            return response()->view('errors.503', [], 503);
        }

        $allowed = strtoupper(trim($allowed));

        // Allow all networks only when explicitly configured.
        if ($allowed === 'ALL') {
            return $next($request);
        }

        // Convert configured networks into an array.
        $networks = array_filter(
            array_map('trim', explode(',', $allowed))
        );

        // Check whether the client's IP matches an allowed network.
        foreach ($networks as $network) {
            if ($this->ipMatches($clientIp, $network)) {
                return $next($request);
            }
        }

        // Block requests from unauthorized networks.
        return response()->view('errors.503', [], 503);
    }

    /**
     * Check whether an IP matches an exact IP, CIDR or legacy prefix.
     */
    protected function ipMatches(string $ip, string $rule): bool
    {
        // Exact IP match.
        if ($ip === $rule) {
            return true;
        }

        // CIDR match.
        if (str_contains($rule, '/')) {
            return $this->cidrMatch($ip, $rule);
        }

        // Legacy prefix match.
        return str_starts_with($ip, $rule);
    }

    /**
     * Check whether an IPv4 address belongs to a CIDR range.
     */
    protected function cidrMatch(string $ip, string $cidr): bool
    {
        $parts = explode('/', $cidr, 2);

        if (count($parts) !== 2) {
            return false;
        }

        [$subnet, $mask] = $parts;

        if (
            filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) === false ||
            filter_var($subnet, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) === false ||
            !ctype_digit($mask) ||
            (int) $mask < 0 ||
            (int) $mask > 32
        ) {
            return false;
        }

        $ipLong = ip2long($ip);
        $subnetLong = ip2long($subnet);

        $maskBits = (int) $mask;

        $maskLong = $maskBits === 0
            ? 0
            : (-1 << (32 - $maskBits));

        return (($ipLong & $maskLong) === ($subnetLong & $maskLong));
    }
}
