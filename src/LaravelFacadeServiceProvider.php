<?php

namespace Hmones\LaravelFacade;

use Illuminate\Support\ServiceProvider;

class LaravelFacadeServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot(): void
    {
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'hmones');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'hmones');
        // $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        // $this->loadRoutesFrom(__DIR__.'/routes.php');

        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/laravel-facade.php', 'laravel-facade');

        // Register the service the package provides.
        $this->app->singleton('laravel-facade', function ($app) {
            return new LaravelFacade;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['laravel-facade'];
    }

    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole(): void
    {
        // Publishing the configuration file.
        $this->publishes([
            __DIR__.'/../config/laravel-facade.php' => config_path('laravel-facade.php'),
        ], 'laravel-facade.config');

        // Publishing the views.
        /*$this->publishes([
            __DIR__.'/../resources/views' => base_path('resources/views/vendor/hmones'),
        ], 'laravel-facade.views');*/

        // Publishing assets.
        /*$this->publishes([
            __DIR__.'/../resources/assets' => public_path('vendor/hmones'),
        ], 'laravel-facade.views');*/

        // Publishing the translation files.
        /*$this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/hmones'),
        ], 'laravel-facade.views');*/

        // Registering package commands.
        // $this->commands([]);
    }
}
