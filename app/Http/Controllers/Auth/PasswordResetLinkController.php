<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordIdentifierRequest;
use App\Http\Requests\Auth\ResetPasswordWithChallengeRequest;
use App\Http\Requests\Auth\VerifyPasswordResetChallengeRequest;
use App\Models\User;
use App\Services\Auth\AuthAuditLogger;
use App\Services\Auth\PasswordResetChallengeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class PasswordResetLinkController extends Controller
{
    public function __construct(
        protected PasswordResetChallengeService $challengeService
    ) {
    }

    public function create()
    {
        return view('auth.forgot-password');
    }

    public function store(ForgotPasswordIdentifierRequest $request)
    {
        $key = 'password-reset-initiate|'.$request->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);

            return back()->withErrors([
                'identifier' => "Too many requests. Please try again in {$seconds} seconds.",
            ]);
        }
        RateLimiter::hit($key, 60);

        AuthAuditLogger::passwordResetInitiated($request);

        $result = $this->challengeService->initiateFromIdentifier(
            $request->input('identifier')
        );

        if (! $result['found']) {
            return back()
                ->withInput($request->only('identifier'))
                ->with('fail', $result['message']);
        }

        return redirect()
            ->route('password.challenge', ['token' => $result['token']])
            ->with('status', 'Please answer all three verification questions to continue.');
    }

    public function showChallenge(Request $request, string $token)
    {
        $payload = $this->challengeService->getPayload($token);

        if (! $payload || empty($payload['user_id'])) {
            return redirect()
                ->route('password.request')
                ->with('fail', 'This password reset request has expired. Please start again.');
        }

        if ($this->challengeService->isBlocked($payload)) {
            return redirect()
                ->route('password.request')
                ->with('fail', 'Too many failed attempts. Please wait before starting a new request.');
        }

        if (! empty($payload['verified'])) {
            return redirect()->route('password.reset.form', ['token' => $token]);
        }

        $challenges = $this->challengeService->publicChallengesFromPayload($payload);

        return view('auth.forgot-password-challenge', [
            'token' => $token,
            'challenges' => $challenges,
            'regenerationsRemaining' => $this->challengeService->regenerationsRemaining($payload),
        ]);
    }

    public function regenerateChallenges(Request $request, string $token)
    {
        $key = 'password-reset-regenerate|'.$request->ip().'|'.$token;

        if (RateLimiter::tooManyAttempts($key, 10)) {
            $seconds = RateLimiter::availableIn($key);

            return redirect()
                ->route('password.challenge', ['token' => $token])
                ->with('fail', "Please wait {$seconds} seconds before requesting new questions.");
        }
        RateLimiter::hit($key, 60);

        $result = $this->challengeService->regenerate($token);

        if (! $result['success']) {
            return redirect()
                ->route('password.challenge', ['token' => $token])
                ->with('fail', $result['message']);
        }

        return redirect()
            ->route('password.challenge', ['token' => $token])
            ->with('status', $result['message']);
    }

    public function verifyChallenge(VerifyPasswordResetChallengeRequest $request)
    {
        $token = $request->input('token');
        $key = 'password-reset-verify|'.$request->ip().'|'.$token;

        if (RateLimiter::tooManyAttempts($key, 10)) {
            $seconds = RateLimiter::availableIn($key);

            return back()->withErrors([
                'answer_math' => "Too many attempts. Please try again in {$seconds} seconds.",
            ]);
        }
        RateLimiter::hit($key, 60);

        $result = $this->challengeService->verify($token, $request->challengeAnswers());

        if (! empty($result['verified'])) {
            return redirect()
                ->route('password.reset.form', ['token' => $token])
                ->with('status', $result['message']);
        }

        return back()
            ->withInput()
            ->withErrors(['verification' => $result['message']]);
    }

    public function showResetForm(Request $request, string $token)
    {
        if (! $this->challengeService->canResetPassword($token)) {
            return redirect()
                ->route('password.request')
                ->with('fail', 'Please complete verification before resetting your password.');
        }

        return view('auth.forgot-password-reset', [
            'token' => $token,
        ]);
    }

    public function resetPassword(ResetPasswordWithChallengeRequest $request)
    {
        $token = $request->input('token');

        if (! $this->challengeService->canResetPassword($token)) {
            return redirect()
                ->route('password.request')
                ->with('fail', 'Your reset session is invalid or has expired. Please start again.');
        }

        $userId = $this->challengeService->userIdForToken($token);
        $user = User::find($userId);

        if (! $user) {
            return redirect()
                ->route('password.request')
                ->with('fail', 'Unable to reset password. Please start again.');
        }

        $user->forceFill([
            'password' => Hash::make($request->input('password')),
            'remember_token' => Str::random(60),
            'password_updated' => 1,
        ])->save();

        $this->challengeService->forget($token);
        AuthAuditLogger::passwordResetCompleted($user->id);

        return redirect()
            ->route('login')
            ->with('success', 'Your password has been reset. Please sign in with your new password.');
    }
}
