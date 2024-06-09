<?php

namespace Tests;

use Illuminate\Support\Facades\Config;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Testing\Traits\CanConfigureMigrationCommands;

trait Traits
{
    public function disableEmailVerification(){
        Config::set('devdojo.auth.settings.registration_require_email_verification', false);
    }
}