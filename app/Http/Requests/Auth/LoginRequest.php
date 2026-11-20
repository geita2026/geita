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
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_name' => ['required', 'string', 'max:100'],
            'password' => ['required', 'string', 'max:100'],
        ];
    }

    /**
     * @throws ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $identifier = trim((string) $this->input('user_name'));
        $user = User::query()
            ->where(function ($query) use ($identifier) {
                $query->where('email', $identifier)
                    ->orWhere('phone_number', $identifier);
            })
            ->first();

        if (! $user || ! Hash::check($this->password, $user->password)) {
            AuthAuditLogger::loginFailed($this, $identifier, 'invalid_credentials');
            throw ValidationException::withMessages([
                'password' => 'Incorrect username or password.',
            ]);
        }

        if ((int) $user->account_status === 0) {
            AuthAuditLogger::loginFailed($this, $identifier, 'suspended');
            throw ValidationException::withMessages([
                'password' => 'Your account cannot be used to sign in at this time. Please contact the system administrator.',
            ]);
        }

        if ((int) $user->account_status === 2) {
            AuthAuditLogger::loginFailed($this, $identifier, 'not_activated');
            throw ValidationException::withMessages([
                'password' => 'Your account is not yet activated. Please check your email for activation instructions.',
            ]);
        }

        if ((int) $user->account_status !== 1) {
            AuthAuditLogger::loginFailed($this, $identifier, 'inactive');
            throw ValidationException::withMessages([
                'password' => 'Incorrect username or password.',
            ]);
        }

        $logins = logins::where('requesting_ip', $this->ip())->where('user_email', $identifier)->first();
        if ($logins) {
            logins::find($logins->id)?->delete();
        }

        Auth::login($user, $this->boolean('remember'));
        RateLimiter::clear($this->throttleKey());
        AuthAuditLogger::loginSuccess($this, $user->email);
    }

    /**
     * @throws ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'password' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    public function throttleKey(): string
    {
        return Str::lower($this->input('user_name')).'|'.$this->ip();
    }

    public function hitRateLimiter(): void
    {
        RateLimiter::hit($this->throttleKey(), 60);
    }
}
