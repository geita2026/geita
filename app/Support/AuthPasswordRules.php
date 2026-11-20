<?php

namespace App\Support;

use Illuminate\Validation\Rules\Password;

class AuthPasswordRules
{
    /**
     * Strong password rule used across registration, change password, and reset.
     */
    public static function rule(): Password
    {
        return Password::min(8)
            ->mixedCase()
            ->letters()
            ->numbers()
            ->symbols()
            ->uncompromised();
    }

    /**
     * @return array<int, mixed>
     */
    public static function requiredConfirmed(): array
    {
        return ['required', 'confirmed', self::rule()];
    }
}
