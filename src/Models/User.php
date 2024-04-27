<?php

namespace Devdojo\Auth\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Devdojo\Auth\Traits\HasSocialProviders;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasSocialProviders;

    public function hasVerifiedEmail()
    {
        if (!config('devdojo.auth.settings.registration_require_email_verification')) {
            return true;
        }

        return $this->email_verified_at !== null;
    }
}
