<?php

namespace App\Http\Middleware;

use App\Models\setting\DefaultSettings;
use Closure;
use Illuminate\Http\Request;

class NetworkMaintenance
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
{
    if (app()->environment('local') && in_array($request->ip(), ['127.0.0.1', '::1'], true)) {
        return $next($request);
    }

    $allowed = optional(DefaultSettings::first())->allowedNetworks;

    // Fail closed if not configured
    if (empty($allowed)) {
        return response()->view('errors.503', [], 503);
    }

    // Normalize
    $allowed = strtoupper(trim($allowed));

    // Allow everything
    if ($allowed === 'ALL') {
        return $next($request);
    }

    $clientIp = $request->ip();

    $networks = array_filter(
        array_map('trim', explode(',', $allowed))
    );

    foreach ($networks as $network) {
        if ($this->ipMatches($clientIp, $network)) {
            return $next($request);
        }
    }

    return response()->view('errors.503', [], 503);
}

protected function ipMatches(string $ip, string $rule): bool
{
    // Exact IP match
    if ($ip === $rule) {
        return true;
    }

    // CIDR match (e.g. 10.1.79.0/24)
    if (str_contains($rule, '/')) {
        return $this->cidrMatch($ip, $rule);
    }

    // Prefix match (legacy support: 10.1.79.)
    return str_starts_with($ip, $rule);
}

protected function cidrMatch(string $ip, string $cidr): bool
{
    [$subnet, $mask] = explode('/', $cidr);

    $ipLong     = ip2long($ip);
    $subnetLong = ip2long($subnet);
    $maskLong   = -1 << (32 - (int) $mask);

    return ($ipLong & $maskLong) === ($subnetLong & $maskLong);
}



}
