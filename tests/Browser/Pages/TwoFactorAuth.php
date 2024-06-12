<?php

namespace Devdojo\Auth\Tests\Browser\Pages;

use Laravel\Dusk\Page;

class TwoFactorAuth extends Page
{
    /**
     * Get the URL for the page.
     */
    public function url(): string
    {
        return '/user/two-factor-authentication';
    }
}
