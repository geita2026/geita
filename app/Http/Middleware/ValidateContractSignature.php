<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ValidateContractSignature
{
    public function handle(Request $request, Closure $next)
    {
        $signature = $request->header('X-Signature');
        $timestamp = $request->header('X-Timestamp');

        if (!$signature || !$timestamp) {
            return response()->json([
                'status' => 0,
                'message' => 'Missing signature or timestamp header'
            ], 401);
        }

        if (abs(time() - $timestamp) > 300) {
            return response()->json([
                'status' => 0,
                'message' => 'Request timestamp expired'
            ], 401);
        }

        $secret = config('services.contract_api.secret');

        $method = $request->method();
        $uri = $request->getRequestUri();
        $body = $request->getContent();

        $payload = $method . $uri . $body . $timestamp;
        $expectedSignature = hash_hmac('sha256', $payload, $secret);

        if (!hash_equals($expectedSignature, $signature)) {
            return response()->json([
                'status' => 0,
                'message' => 'Invalid signature'
            ], 401);
        }

        return $next($request);
    }
}
