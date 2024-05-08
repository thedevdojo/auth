<?php

namespace Devdojo\Auth;

use Illuminate\Support\Facades\Route;
use Livewire\Livewire;
use Livewire\Volt\Volt;
use Laravel\Folio\Folio;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Devdojo\Auth\Http\Controllers\TwoFactorAuthenticationController;
use PragmaRX\Google2FA\Google2FA;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        
        /*
         * Optional methods to load your package assets
         */
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'auth');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'auth');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        $this->registerAuthFolioDirectory();
        $this->registerVoltDirectory();

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/' => config_path('/'),
            ], 'auth:config');

            // Publishing the views.
            /*$this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/auth'),
            ], 'views');*/

            // Publishing assets.
            $this->publishes([
                __DIR__.'/../public' => public_path('auth'),
            ], 'auth:assets');

            // Publishing the translation files.
            /*$this->publishes([
                __DIR__.'/../resources/lang' => resource_path('lang/vendor/auth'),
            ], 'lang');*/

            // Registering package commands.
            // $this->commands([]);
        }
        if (! $this->app->runningInConsole()) {
            Livewire::component('auth.setup.logo', \Devdojo\Auth\Livewire\Setup\Logo::class);
            Livewire::component('auth.setup.background', \Devdojo\Auth\Livewire\Setup\Background::class);
            Livewire::component('auth.setup.color', \Devdojo\Auth\Livewire\Setup\Color::class);
            Livewire::component('auth.setup.alignment', \Devdojo\Auth\Livewire\Setup\Alignment::class);
            Livewire::component('auth.setup.favicon', \Devdojo\Auth\Livewire\Setup\Favicon::class);
            Livewire::component('auth.setup.css', \Devdojo\Auth\Livewire\Setup\Css::class);
        }

        // Register routes for 2FA setup and challenge
        Route::middleware(['web', 'auth'])->group(function () {
            Route::get('two-factor-challenge', [TwoFactorAuthenticationController::class, 'showTwoFactorChallengeForm'])->name('two-factor.login');
            Route::post('two-factor-challenge', [TwoFactorAuthenticationController::class, 'verifyTwoFactorChallenge'])->name('two-factor.verify');
        });
    }

    private function registerAuthFolioDirectory(){
        $pagesDirectory = __DIR__ . '/../resources/views/pages';
        if (File::exists($pagesDirectory)) {
            Folio::path($pagesDirectory)->middleware([
                '*' => [
                    //
                ],
            ]);
        }
    }

    private function registerVoltDirectory(){
        Volt::mount([
            __DIR__ . '/../resources/views/pages'
        ]);
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__.'/../config/devdojo/auth/settings.php', 'devdojo.auth.settings');
        $this->mergeConfigFrom(__DIR__.'/../config/devdojo/auth/appearance.php', 'devdojo.auth.appearance');
        $this->mergeConfigFrom(__DIR__.'/../config/devdojo/auth/language.php', 'devdojo.auth.language');
        $this->mergeConfigFrom(__DIR__.'/../config/devdojo/auth/providers.php', 'devdojo.auth.providers');

        $this->mergeConfigFrom(__DIR__.'/../config/devdojo/auth/descriptions.php', 'devdojo.auth.descriptions');
        $this->mergeConfigFrom(__DIR__.'/../config/devdojo/auth/pages.php', 'devdojo.auth.pages');

        // Register the main class to use with the facade
        $this->app->singleton('devdojoauth', function () {
            return new DevDojoAuth;
        });

        // Bind a singleton for the Google2FA service
        $this->app->singleton(Google2FA::class, function ($app) {
            return new Google2FA();
        });
    }
}
