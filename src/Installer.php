<?php

namespace Devdojo\Auth;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Artisan;

class Installer extends ServiceProvider
{
    public function postInstall(){
        Artisan::call('vendor:publish --tag=auth:assets');
        Artisan::call('vendor:publish --tag=auth:config');
    }

    public function postUpdate(){
        Artisan::call('vendor:publish --tag=auth:assets');
    }
}