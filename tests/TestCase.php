<?php

namespace Hmones\LaravelFacade\Tests;

use Hmones\LaravelFacade\LaravelFacadeServiceProvider;
use Orchestra\Testbench\TestCase as Test;

class TestCase extends Test
{
    protected $serviceProviderPath;
    protected $serviceProviderClass;
    protected $facadeClassPath;

    public function test_package_files_are_published_correctly(): void
    {
        $this->assertTrue(true);
    }

    public function setUp(): void
    {
        parent::setUp();
        $providerDirectory = config('laravel-facade.provider.namespace');
        $this->serviceProviderPath = app_path(
            str_replace($this->getNamespace($providerDirectory).'\\', '', $providerDirectory).'/'.config('laravel-facade.provider.name').'.php'
        );
        $this->facadeClassPath = app_path('Facades/TestFacade.php');
        $this->serviceProviderClass = config('laravel-facade.provider.namespace').'\\'.config('laravel-facade.provider.name').'::class';
    }

    protected function getNamespace($name): string
    {
        return trim(implode('\\', array_slice(explode('\\', $name), 0, -1)), '\\');
    }

    protected function getPackageProviders($app)
    {
        return [
            LaravelFacadeServiceProvider::class,
        ];
    }
}
