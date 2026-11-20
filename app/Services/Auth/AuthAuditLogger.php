<?php

namespace App\Services\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AuthAuditLogger
{
    public static function loginSuccess(Request $request, string $email): void
    {
        Log::info('auth.login.success', [
            'email' => $email,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
    }

    public static function loginFailed(Request $request, string $identifier, ?string $reason = null): void
    {
        Log::warning('auth.login.failed', [
            'identifier' => $identifier,
            'ip' => $request->ip(),
            'reason' => $reason,
        ]);
    }

    public static function passwordResetInitiated(Request $request): void
    {
        Log::info('auth.password_reset.initiated', [
            'ip' => $request->ip(),
        ]);
    }

    public static function passwordResetCompleted(int $userId): void
    {
        Log::info('auth.password_reset.completed', [
            'user_id' => $userId,
        ]);
    }
}
