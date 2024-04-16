<?php

namespace Devdojo\Auth;

use Illuminate\Support\ServiceProvider;
use Laravel\Folio\Folio;
use Illuminate\Support\Facades\File;

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
        // $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        // $this->loadRoutesFrom(__DIR__.'/routes.php');

        $this->registerAuthFolioDirectory();

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/config.php' => config_path('devdojoauth.php'),
            ], 'config');

            // Publishing the views.
            /*$this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/auth'),
            ], 'views');*/

            // Publishing assets.
            $this->publishes([
                __DIR__.'/../public' => public_path('auth'),
            ], 'auth');

            // Publishing the translation files.
            /*$this->publishes([
                __DIR__.'/../resources/lang' => resource_path('lang/vendor/auth'),
            ], 'lang');*/

            // Registering package commands.
            // $this->commands([]);
        }
        
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

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'auth');

        // Register the main class to use with the facade
        $this->app->singleton('devdojoauth', function () {
            return new DevDojoAuth;
        });
    }
}
