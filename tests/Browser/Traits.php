<?php

namespace Tests;

use Illuminate\Support\Facades\Config;

trait Traits
{
    public function disableEmailVerification()
    {
        Config::set('devdojo.auth.settings.registration_require_email_verification', false);
    }
}
