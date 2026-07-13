<?php

namespace Tests;

use Illuminate\Contracts\Console\Kernel;
use RuntimeException;

trait CreatesApplication
{
    public function createApplication()
    {
        $app = require $this->resolveApplicationBasePath().'/bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        return $app;
    }

    protected function resolveApplicationBasePath(): string
    {
        $dir = __DIR__;

        while (! is_file($dir.'/bootstrap/app.php')) {
            $parent = dirname($dir);

            if ($parent === $dir) {
                throw new RuntimeException('Unable to locate Laravel application bootstrap/app.php.');
            }

            $dir = $parent;
        }

        return $dir;
    }
}
