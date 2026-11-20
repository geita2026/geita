<?php

namespace App\Http\Requests\Auth;

use App\Support\AuthPasswordRules;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class ResetPasswordWithChallengeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'token' => ['required', 'string', 'min:32'],
            'password' => AuthPasswordRules::requiredConfirmed(),
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $userId = app(\App\Services\Auth\PasswordResetChallengeService::class)
                ->userIdForToken((string) $this->input('token'));

            if (! $userId) {
                return;
            }

            $user = \App\Models\User::find($userId);
            if ($user && \Illuminate\Support\Facades\Hash::check($this->input('password'), $user->password)) {
                $validator->errors()->add('password', 'The new password must be different from your current password.');
            }
        });
    }
}
