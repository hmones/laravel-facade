<?php

namespace Hmones\LaravelFacade;

use Hmones\LaravelFacade\Console\FacadeMakeCommand;
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
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }

    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole(): void
    {
        // Publishing the configuration file.
//        $this->publishes([
//            __DIR__ . '/../config/laravel-facade.php' => config_path('laravel-facade.php'),
//        ], 'laravel-facade.config');

        $this->publishes([
            __DIR__ . '/Providers/FacadeServiceProvider.php' => app_path('Providers/FacadeServiceProvider.php'),
        ], 'laravel-facade-provider');

        $this->commands([
            FacadeMakeCommand::class
        ]);
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/laravel-facade.php', 'laravel-facade');

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
}
