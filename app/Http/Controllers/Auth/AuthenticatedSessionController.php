<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\logins;
use App\Models\settings\events;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use App\Services\Auth\AuthAuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
    public function create()
    {
        return view('auth.LoginPage');
    }

    public function store(LoginRequest $request)
    {
        try {
            $event = events::where('event_status', 1)->first();
            if ($event) {
                session(['liveEvent' => $event->event_id]);
            }

            $ip = $request->ip();
            $userName = trim((string) $request->user_name);
            $waitingTime = 5;
            $maxAttempts = 5;

            $attempt = logins::where('requesting_ip', $ip)->where('user_email', $userName)->first();

            if ($attempt && (int) $attempt->blocked === 1) {
                if ((date('Ymdhi') - $attempt->blocked_at) >= $waitingTime) {
                    logins::find($attempt->id)?->delete();
                } else {
                    AuthAuditLogger::loginFailed($request, $userName, 'ip_blocked');

                    return back()->withErrors([
                        'password' => 'Too many login attempts. Please try again shortly.',
                    ])->onlyInput('user_name');
                }
            }

            try {
                $request->authenticate();
            } catch (ValidationException $e) {
                $request->hitRateLimiter();
                $this->saveAttempts($userName, $ip, $request->password, $maxAttempts);
                throw $e;
            }

            $ipAddress = $request->ip();
            User::query()
                ->where(function ($query) use ($userName) {
                    $query->where('email', $userName)
                        ->orWhere('phone_number', $userName);
                })
                ->update(['user_ip_address' => $ipAddress]);

            $request->session()->regenerate();

            $request->session()->forget('url.intended');

            return redirect(RouteServiceProvider::HOME)->with('success', 'Successfully logged in');
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->onlyInput('user_name');
        } catch (\Throwable $th) {
            AuthAuditLogger::loginFailed($request, (string) $request->user_name, 'exception');

            return back()->withErrors([
                'password' => 'Incorrect username or password.',
            ])->onlyInput('user_name');
        }
    }

    public function destroy(Request $request)
    {
        if (Auth::check()) {
            $email = Auth::user()->email;
            User::where('email', $email)->update(['user_ip_address' => null]);
            logins::where('user_email', $email)->delete();
        }

        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Logged out successfully.');
    }

    public function saveAttempts($user_name, $ip, $password, $max_attempts): void
    {
        $attempt = logins::where('requesting_ip', $ip)->where('user_email', $user_name)->first();

        if ($attempt) {
            $count = $attempt->login_attempts_count + 1;
            $blocked = $count >= $max_attempts ? 1 : 0;
            $time = $blocked ? date('Ymdhi') : null;

            $passwords = is_array($attempt->password_submited)
                ? $attempt->password_submited
                : [$attempt->password_submited];
            $passwords[] = $password;

            logins::find($attempt->id)->update([
                'login_attempts_count' => $count,
                'blocked' => $blocked,
                'blocked_at' => $time,
                'password_submited' => $passwords,
            ]);
        } else {
            logins::create([
                'user_email' => $user_name,
                'status' => 0,
                'login_at' => date('Y-m-d H:i:s'),
                'login_attempts_count' => 1,
                'requesting_ip' => $ip,
                'password_submited' => $password,
            ]);
        }
    }
}
