<?php

namespace App\Http\Requests\Auth;

use App\Services\Auth\PasswordResetChallengeService;
use Illuminate\Foundation\Http\FormRequest;

class VerifyPasswordResetChallengeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'token' => ['required', 'string', 'min:32'],
        ];

        $payload = app(PasswordResetChallengeService::class)->getPayload((string) $this->input('token'));

        foreach ($payload['challenges'] ?? [] as $challenge) {
            $field = 'answer_'.$challenge['key'];
            $key = $challenge['key'];

            if (str_starts_with($key, PasswordResetChallengeService::CHALLENGE_MATH)) {
                $rules[$field] = ['required', 'string', 'max:10', 'regex:/^-?\d+$/'];
            } elseif ($key === PasswordResetChallengeService::CHALLENGE_EMAIL) {
                $rules[$field] = ['required', 'email', 'max:100'];
            } elseif ($key === PasswordResetChallengeService::CHALLENGE_PHONE) {
                $rules[$field] = ['required', 'string', 'max:20'];
            } else {
                $rules[$field] = ['required', 'string', 'max:200'];
            }
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'regex' => 'Enter numbers only for the math question.',
            'email' => 'Enter a valid email address.',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function challengeAnswers(): array
    {
        $answers = [];
        $payload = app(PasswordResetChallengeService::class)->getPayload((string) $this->input('token'));

        foreach ($payload['challenges'] ?? [] as $challenge) {
            $answers[$challenge['key']] = (string) $this->input('answer_'.$challenge['key']);
        }

        return $answers;
    }
}
