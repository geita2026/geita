<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ValidateContractApiKey
{
    public function handle(Request $request, Closure $next)
    {
        $apiKey = $request->header('X-API-Key') ?? $request->query('api_key');

        $validApiKey = config('services.contract_api.key');

        if (!$apiKey || $apiKey !== $validApiKey) {
            return response()->json([
                'status' => 0,
                'message' => 'Unauthorized. Invalid API key.'
            ], 401);
        }

        return $next($request);
    }
}
