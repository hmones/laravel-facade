<?php

namespace Hmones\LaravelFacade;

use Hmones\LaravelFacade\Console\FacadeMakeCommand;
use Hmones\LaravelFacade\Console\FacadeRemoveCommand;
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
        $this->publishes([
            __DIR__.'/../config/laravel-facade.php' => config_path('laravel-facade.php'),
        ], 'laravel-facade-config');

        $this->publishes([
            __DIR__.'/Providers/FacadeServiceProvider.php' => app_path('Providers/FacadeServiceProvider.php'),
        ], 'laravel-facade-provider');

        $this->commands([
            FacadeMakeCommand::class,
            FacadeRemoveCommand::class,
        ]);
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/laravel-facade.php', 'laravel-facade');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            FacadeMakeCommand::class,
        ];
    }
}
