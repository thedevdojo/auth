<?php

namespace Devdojo\Auth\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Dusk\Browser;
use Laravel\Dusk\Dusk;

class DuskServiceProvider extends ServiceProvider
{
    /**
     * Register Dusk's browser macros.
     */
    public function boot(): void
    {
        Dusk::selectorHtmlAttribute('data-auth');

        Browser::macro('authAttributeChange', function (?string $selector, string $attribute, string $value) {
            $this->script("document.querySelector('$selector').setAttribute('$attribute', '$value');");

            return $this;
        });

        Browser::macro('authAttributeRemove', function (?string $selector, string $attribute) {
            $this->script("document.querySelector('$selector').removeAttribute('$attribute');");

            return $this;
        });

        Browser::macro('testValidationErrorOnSubmit', function (string $message = '') {
            $this
                ->click('@submit-button')
                ->waitForText($message)
                ->assertSee($message);

            return $this;
        });

        Browser::macro('createJohnDoe', function () {
            $user = \App\Models\User::factory()->create([
                'email' => 'johndoe@gmail.com',
                'password' => \Hash::make('password'),
            ]);

            return $this;
        });

        Browser::macro('loginAsJohnDoe', function () {
            $this->loginAs(\Devdojo\Auth\Models\User::where('email', 'johndoe@gmail.com')->first());

            return $this;
        });

        Browser::macro('enable2FAforJohnDoe', function () {
            $johnDoe = \Devdojo\Auth\Models\User::where('email', 'johndoe@gmail.com')->first();
            $johnDoe->two_factor_confirmed_at = now();
            $johnDoe->save();

            return $this;
        });

        Browser::macro('assertRedirectAfterAuthUrlIsCorrect', function () {
            $redirectExpectedToBe = '/';
            if (class_exists(\Devdojo\Genesis\Genesis::class)) {
                $redirectExpectedToBe = '/dashboard';
            }
            $this->assertPathIs($redirectExpectedToBe);

            return $this;
        });

        Browser::macro('clearLogFile', function () {
            file_put_contents(storage_path('logs/laravel.log'), '');

            return $this;
        });

        Browser::macro('getLogFile', function ($callback) {
            $content = file_get_contents(storage_path('logs/laravel.log'));
            $callback($content);

            return $this;
        });

        Browser::macro('typeAndSubmit', function (?string $selector, string $value) {
            $this->type($selector, $value)
                ->click('@submit-button');

            return $this;
        });

        Browser::macro('visitPasswordConfirmTestPage', function () {
            $this->visit('/auth/password_confirmation_test');

            return $this;
        });

    }
}
