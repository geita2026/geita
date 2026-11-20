<?php

namespace App\Http\Requests\Auth;

use App\Support\AuthPasswordRules;
use Illuminate\Foundation\Http\FormRequest;

class RegisterUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'firstName' => ['required', 'string', 'max:100'],
            'lastName' => ['required', 'string', 'max:100'],
            'middleName' => ['nullable', 'string', 'max:100'],
            'phoneNumber' => ['required', 'string', 'max:20', 'unique:users,phone_number'],
            'userEmail' => ['required', 'email', 'max:100', 'unique:users,email'],
            'password' => AuthPasswordRules::requiredConfirmed(),
        ];
    }

    public function messages(): array
    {
        return [
            'userEmail.unique' => 'An account with this email already exists. Please sign in or recover your password.',
            'phoneNumber.unique' => 'An account with this phone number already exists. Please sign in or use a different number.',
        ];
    }
}
