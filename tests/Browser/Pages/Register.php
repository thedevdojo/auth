<?php

namespace Devdojo\Auth\Tests\Browser\Pages;

use Laravel\Dusk\Page;
use Laravel\Dusk\Browser;
use Illuminate\Support\Facades\Config;

class Register extends Page
{
    protected function setUp(): void
    {
        dd('cool!');
    }
    /**
     * Get the URL for the page.
     */
    public function url(): string
    {
        return '/auth/register';
    }

    public function registerAsJohnDoe(Browser $browser){
        $redirectExpectedToBe = '/';
        if(class_exists(\Devdojo\Genesis\Genesis::class)){
            $redirectExpectedToBe = '/dashboard';
        }
        $browser
            ->type('@email-input', 'johndoe@gmail.com')
            ->type('@password-input', 'password')
            ->clickAndWaitForReload('@submit-button');

    }
}
