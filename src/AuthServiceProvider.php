<?php

namespace Devdojo\Auth;

use Devdojo\Auth\Http\Middleware\TwoFactorChallenged;
use Devdojo\Auth\Http\Middleware\TwoFactorEnabled;
use Devdojo\Auth\Http\Middleware\ViewAuthSetup;
use Devdojo\Auth\Livewire\Setup\Alignment;
use Devdojo\Auth\Livewire\Setup\Background;
use Devdojo\Auth\Livewire\Setup\Color;
use Devdojo\Auth\Livewire\Setup\Css;
use Devdojo\Auth\Livewire\Setup\Favicon;
use Devdojo\Auth\Livewire\Setup\Logo;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Features;
use Laravel\Passkeys\PasskeysServiceProvider;
use Livewire\Livewire;
use PragmaRX\Google2FA\Google2FA;

class AuthServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Route::middlewareGroup('two-factor-challenged', [TwoFactorChallenged::class]);
        Route::middlewareGroup('two-factor-enabled', [TwoFactorEnabled::class]);
        Route::middlewareGroup('view-auth-setup', [ViewAuthSetup::class]);

        Blade::anonymousComponentPath(
            __DIR__.'/../resources/views/components',
            'auth'
        );
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'auth');
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        Livewire::addLocation(viewPath: __DIR__.'/../resources/views/pages');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/' => config_path('/'),
            ], 'auth:config');

            $this->publishes([
                __DIR__.'/../public' => public_path('auth'),
            ], 'auth:assets');

            $this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/auth'),
            ], 'auth:views');

            $this->publishes([
                __DIR__.'/../resources/workflows' => base_path('.github/workflows'),
            ], 'auth:ci');

            $this->publishes([
                __DIR__.'/../database/migrations' => database_path('migrations'),
            ], 'auth:migrations');

            $this->publishes([
                __DIR__.'/../resources/views/components/elements' => resource_path('views/components/auth/elements'),
            ], 'auth:components');

            if (class_exists(PasskeysServiceProvider::class)) {
                $this->publishes([
                    __DIR__.'/../config/passkeys.php' => config_path('passkeys.php'),
                ], 'auth:passkeys-config');
            }
        }

        if (! $this->app->runningInConsole()) {
            Livewire::component('auth.setup.logo', Logo::class);
            Livewire::component('auth.setup.background', Background::class);
            Livewire::component('auth.setup.color', Color::class);
            Livewire::component('auth.setup.alignment', Alignment::class);
            Livewire::component('auth.setup.favicon', Favicon::class);
            Livewire::component('auth.setup.css', Css::class);
        }

        $this->handleStarterKitFunctionality();
        $this->loadDynamicRoutesForTesting();
    }

    private function handleStarterKitFunctionality(): void
    {
        if (class_exists(Features::class) && config('devdojo.auth.settings.enable_2fa')) {
            Config::set('fortify.features', array_merge(
                Config::get('fortify.features', []),
                [
                    Features::twoFactorAuthentication([
                        'confirm' => true,
                        'confirmPassword' => true,
                    ]),
                ]
            ));
        }
    }

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/devdojo/auth/settings.php', 'devdojo.auth.settings');
        $this->mergeConfigFrom(__DIR__.'/../config/devdojo/auth/appearance.php', 'devdojo.auth.appearance');
        $this->mergeConfigFrom(__DIR__.'/../config/devdojo/auth/language.php', 'devdojo.auth.language');
        $this->mergeConfigFrom(__DIR__.'/../config/devdojo/auth/providers.php', 'devdojo.auth.providers');
        $this->mergeConfigFrom(__DIR__.'/../config/devdojo/auth/descriptions.php', 'devdojo.auth.descriptions');

        if (class_exists(PasskeysServiceProvider::class)) {
            $this->mergeConfigFrom(__DIR__.'/../config/passkeys.php', 'passkeys');
        }

        $this->app->singleton('devdojoauth', function () {
            return new Auth;
        });

        $this->app->singleton(Google2FA::class, function () {
            return new Google2FA;
        });

        config()->set('livewire.inject_assets', true);
    }

    private function loadDynamicRoutesForTesting(): void
    {
        if (app()->environment('testing') || app()->environment('local')) {
            Route::get('/auth/password_confirmation_test', function () {
                return 'Test Confirmed';
            })->middleware('web', 'auth', 'password.confirm');
        }
    }
}
