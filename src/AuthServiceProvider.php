<?php

namespace Devdojo\Auth;

use Livewire\Livewire;
use Livewire\Volt\Volt;
use Laravel\Folio\Folio;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;

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
        Livewire::component('auth.setup.logo', \Devdojo\Auth\Livewire\Setup\Logo::class);
        
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

        // Register the main class to use with the facade
        $this->app->singleton('devdojoauth', function () {
            return new DevDojoAuth;
        });
    }
}
