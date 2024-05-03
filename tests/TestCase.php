<?php

namespace Devdojo\Auth\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Devdojo\Auth\AuthServiceProvider;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            AuthServiceProvider::class,
        ];
    }
}