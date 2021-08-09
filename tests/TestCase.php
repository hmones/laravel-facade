<?php

namespace Hmones\LaravelFacade\Tests;

use Hmones\LaravelFacade\LaravelFacadeServiceProvider;
use Orchestra\Testbench\TestCase as Test;

class TestCase extends Test
{
    public function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app)
    {
        return [
            LaravelFacadeServiceProvider::class,
        ];
    }
}
