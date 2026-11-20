<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class ForgotPasswordIdentifierRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'identifier' => [
                'required',
                'string',
                'max:100',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    $value = trim((string) $value);
                    $isEmail = (bool) filter_var($value, FILTER_VALIDATE_EMAIL);
                    $digits = preg_replace('/\D/', '', $value) ?? '';
                    $isPhone = strlen($digits) >= 9 && preg_match('/^[0-9+\s\-()]+$/', $value);

                    if (! $isEmail && ! $isPhone) {
                        $fail('Enter a valid registered email address or phone number.');
                    }
                },
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'identifier.required' => 'Please enter your registered email address or phone number.',
        ];
    }
}
