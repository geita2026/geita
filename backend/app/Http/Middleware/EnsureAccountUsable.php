<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * A token only works while its account is active, and an account that owes a
 * password change can do nothing except change it (FR-046).
 */
class EnsureAccountUsable
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (! $user->is_active || $user->locked_at) {
            if (method_exists($user->currentAccessToken(), 'delete')) {
                $user->currentAccessToken()->delete();
            }

            return response()->json(['message' => 'This account is no longer active.'], 401);
        }
        if ($user->must_change_password && ! $request->is('api/v1/auth/*')) {
            return response()->json(['message' => 'Change your temporary password to continue.', 'code' => 'password_change_required'], 403);
        }

        return $next($request);
    }
}
