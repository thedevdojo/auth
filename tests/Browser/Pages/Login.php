<?php

namespace Devdojo\Auth\Tests\Browser\Pages;

use Laravel\Dusk\Browser;
use Laravel\Dusk\Page;

class Login extends Page
{
    /**
     * Get the URL for the page.
     */
    public function url(): string
    {
        return '/auth/login';
    }

    public function formLoginAsJohnDoe(Browser $browser)
    {
        $browser
            ->visit('/auth/login')
            ->type('@email-input', 'johndoe@gmail.com')
            ->click('@submit-button')
            ->waitFor('@password-input')
            ->type('@password-input', 'password')
            ->clickAndWaitForReload('@submit-button');

        return $this;
    }
}
