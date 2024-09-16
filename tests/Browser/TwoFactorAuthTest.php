<?php

use Devdojo\Auth\Tests\Browser\Pages\TwoFactorAuth;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;

uses(DatabaseMigrations::class);

test('Redirected to login if not authenticated', function () {

    $this->browse(function (Browser $browser) {
        $browser->visit(new TwoFactorAuth)
            ->assertPathIs('/auth/login');
    });
});

test('Successfully View 2FA Setup Page', function () {
    $this->setConfig('devdojo.auth.settings.enable_2fa', true);
    $this->browse(function (Browser $browser) {
        $browser
            ->createJohnDoe()
            ->loginAsJohnDoe()
            ->visit(new TwoFactorAuth)
            ->assertSee('Two factor authentication disabled')
            ->click('@enable-button')
            ->waitForText('Finish enabling two factor authentication')
            ->assertSee('Finish enabling two factor authentication');
    });
    $this->resetConfig();
});
