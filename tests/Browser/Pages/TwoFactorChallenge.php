<?php

namespace Devdojo\Auth\Tests\Browser\Pages;

use Laravel\Dusk\Page;

class TwoFactorChallenge extends Page
{
    /**
     * Get the URL for the page.
     */
    public function url(): string
    {
        return '/auth/two-factor-challenge';
    }
}
