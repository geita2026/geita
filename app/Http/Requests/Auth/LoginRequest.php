<?php

namespace App\Http\Requests\Auth;

use App\Models\logins;
use App\Models\User;
use App\Services\Auth\AuthAuditLogger;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Authorize login request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Login validation rules.
     */
    public function rules(): array
    {
        return [
            'user_name' => ['required', 'string', 'max:100'],
            'password' => ['required', 'string', 'max:100'],
            'remember' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Authenticate using email, username or phone number.
     *
     * @throws ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $identifier = trim(
            (string) $this->input('user_name')
        );

        // Find user by email, username or phone.
        $user = User::query()
            ->where(function ($query) use ($identifier) {
                $query->where('email', $identifier)
                    ->orWhere('user_name', $identifier)
                    ->orWhere('phone_number', $identifier);
            })
            ->first();

        // Verify credentials.
        if (
            ! $user ||
            ! $user->password ||
            ! Hash::check(
                (string) $this->input('password'),
                $user->password
            )
        ) {
            RateLimiter::hit(
                $this->throttleKey(),
                60
            );

            AuthAuditLogger::loginFailed(
                $this,
                $identifier,
                'invalid_credentials'
            );

            throw ValidationException::withMessages([
                'user_name' => 'Incorrect email, username, phone number or password.',
            ]);
        }

        // Check account status.
        if ((int) $user->account_status !== 1) {

            RateLimiter::hit(
                $this->throttleKey(),
                60
            );

            $status = (int) $user->account_status;

            $reason = match ($status) {
                0 => 'suspended',
                2 => 'not_activated',
                default => 'inactive',
            };

            AuthAuditLogger::loginFailed(
                $this,
                $identifier,
                $reason
            );

            $message = match ($status) {
                0 => 'Your account is suspended. Please contact the administrator.',
                2 => 'Your account is not activated.',
                default => 'Your account is not active.',
            };

            throw ValidationException::withMessages([
                'user_name' => $message,
            ]);
        }

        // Remove previous login record for this IP and user.
        $previousLogin = logins::query()
            ->where('requesting_ip', $this->ip())
            ->where('user_email', $user->email)
            ->first();

        if ($previousLogin) {
            $previousLogin->delete();
        }

        // Login user.
        Auth::guard('web')->login(
            $user,
            $this->boolean('remember')
        );

        // Regenerate session for security.
        $this->session()->regenerate();

        // Clear failed login attempts.
        RateLimiter::clear(
            $this->throttleKey()
        );

        // Record successful login.
        AuthAuditLogger::loginSuccess(
            $this,
            $user->email
        );
    }

    /**
     * Check login rate limit.
     *
     * @throws ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts(
            $this->throttleKey(),
            5
        )) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn(
            $this->throttleKey()
        );

        throw ValidationException::withMessages([
            'password' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Generate login throttle key.
     */
    public function throttleKey(): string
    {
        return Str::lower(
            trim((string) $this->input('user_name'))
        ) . '|' . $this->ip();
    }

    /**
     * Record failed attempt.
     */
    public function hitRateLimiter(): void
    {
        RateLimiter::hit(
            $this->throttleKey(),
            60
        );
    }
}
