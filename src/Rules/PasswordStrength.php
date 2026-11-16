<?php

namespace Devdojo\Auth\Rules;

use Illuminate\Validation\Rules\Password;

class PasswordStrength
{
    /**
     * Build a password validation rule based on config settings.
     */
    public static function rule(): Password
    {
        $minLength = config('devdojo.auth.settings.password_min_length') ?? 8;
        $rule = Password::min($minLength);

        if (config('devdojo.auth.settings.password_require_uppercase', false)) {
            $rule->mixedCase();
        }

        if (config('devdojo.auth.settings.password_require_numeric', false)) {
            $rule->numbers();
        }

        if (config('devdojo.auth.settings.password_require_special_character', false)) {
            $rule->symbols();
        }

        if (config('devdojo.auth.settings.password_require_uncompromised', false)) {
            $rule->uncompromised();
        }

        return $rule;
    }

    /**
     * Get the password validation rules as an array.
     *
     * @param  bool  $confirmed  Whether to include the confirmed rule
     */
    public static function rules(bool $confirmed = false): array
    {
        $rules = ['required', static::rule()];

        if ($confirmed) {
            $rules[] = 'confirmed';
        }

        return $rules;
    }
}
