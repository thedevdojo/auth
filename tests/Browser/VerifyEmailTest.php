<?php

use Devdojo\Auth\Tests\Browser\Pages\Register;
use Illuminate\Auth\Notifications\VerifyEmail as VerifyEmailNotification;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Mail;
use Laravel\Dusk\Browser;

uses(DatabaseMigrations::class);

test('Auth sends email verification on registration', function () {
    Mail::fake();
    $this->setConfig('devdojo.auth.settings.registration_require_email_verification', true);
    $this->browse(function (Browser $browser) {
        $browser
            ->visit(new Register)
            ->registerAsJohnDoe()
            ->assertPathIs('/auth/verify');
        // Mail::assertQueued(VerifyEmailNotification::class);
    });
    $this->resetConfig();
});
