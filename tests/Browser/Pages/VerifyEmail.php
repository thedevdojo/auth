<?php

namespace Devdojo\Auth\Tests\Browser\Pages;

use Laravel\Dusk\Page;
use Laravel\Dusk\Browser;

class VerifyEmail extends Page
{
    /**
     * Get the URL for the page.
     */
    public function url(): string
    {
        return '/auth/verify';
    }

}
