<?php

namespace Devdojo\Auth;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Artisan;

class Installer extends ServiceProvider
{
    public function postInstall(){
        Artisan::call('vendor:publish', ['--tag' => 'auth:assets', '--force' => true]);
        Artisan::call('vendor:publish', ['--tag' => 'auth:config', '--force' => true]);
    }

    public function postUpdate(){
        Artisan::call('vendor:publish', ['--tag' => 'auth:assets', '--force' => true]);
    }
}