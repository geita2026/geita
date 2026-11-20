<?php

namespace App\Http\Requests\Auth;

use App\Support\AuthPasswordRules;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Validator;

class ChangePasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'password' => ['required', 'string'],
            'new_password' => AuthPasswordRules::requiredConfirmed(),
            'confirmation_password' => ['required', 'same:new_password'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $user = Auth::user();
            if (! $user || ! Hash::check($this->input('password'), $user->password)) {
                $validator->errors()->add('password', 'The current password is incorrect.');
            }

            if ($user && Hash::check($this->input('new_password'), $user->password)) {
                $validator->errors()->add('new_password', 'The new password must be different from your current password.');
            }
        });
    }
}
