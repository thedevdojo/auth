<?php

namespace Devdojo\Auth\Traits;

use Illuminate\Contracts\Auth\MustVerifyEmail;

trait HasConditionalEmailVerification
{
    /**
     * Dynamically implement MustVerifyEmail based on configuration.
     */
    public static function bootHasConditionalEmailVerification()
    {
        if (config('devdojo.auth.settings.require_email_verification')) {
            static::classImplements(MustVerifyEmail::class);
        }
    }

    /**
     * Helper to add interface implementation dynamically.
     */
    private static function classImplements($interface)
    {
        if (! isset(class_implements(static::class)[$interface])) {
            class_uses_recursive(static::class)[$interface] = $interface;
        }
    }
}
