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

    public function loginAsJohnDoe(Browser $browser)
    {
        $browser
            ->visit('/auth/login')
            ->type('@email-input', 'johndoe@gmail.com')
            ->click('@submit-button')
            ->waitFor('@password-input')
            ->type('@password-input', 'password')
            ->screenshot('before-submit')
            ->clickAndWaitForReload('@submit-button')
            ->screenshot('after-submit')
            ->assertRedirectAfterAuthUrlIsCorrect();

        return $this;
    }

    public function typeAndSubmit(Browser $browser, $selector, $value)
    {
        $browser->type($selector, $value)
            ->click('@submit-button');

        return $this;
    }
}
