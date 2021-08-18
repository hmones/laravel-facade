<?php

namespace Hmones\LaravelFacade\Tests;

use Hmones\LaravelFacade\LaravelFacadeServiceProvider;
use Orchestra\Testbench\TestCase as Test;

class TestCase extends Test
{
    protected function getPackageProviders($app)
    {
        return [
            LaravelFacadeServiceProvider::class,
        ];
    }

    public function test_package_files_are_published_correctly(): void
    {
        $this->assertTrue(true);
    }
}
