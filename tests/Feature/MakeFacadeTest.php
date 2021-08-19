<?php

namespace Hmones\LaravelFacade\Tests\Feature;

use Hmones\LaravelFacade\Tests\TestCase;
use Illuminate\Support\Facades\File;
use Symfony\Component\Console\Exception\RuntimeException;

class MakeFacadeTest extends TestCase
{
    public function test_facade_is_created_successfully(): void
    {
        // make sure we're starting from a clean state
        if (File::exists($this->facadeClassPath)) {
            unlink($this->facadeClassPath);
        }

        $this->assertFalse(File::exists($this->facadeClassPath));

        // Run the make command
        $this->artisan('make:facade TestFacade ' . 'Hmones\\\LaravelFacade\\\Console\\\FacadeMakeCommand')
            ->expectsOutput('Publishing Facade Service Provider...')
            ->expectsOutput('Updating Facade Service Provider...')
            ->expectsOutput('Facade created successfully.')
            ->execute();

        // Assert a new file is created
        $this->assertTrue(File::exists($this->facadeClassPath));
        $this->assertTrue(File::exists($this->serviceProviderPath));

        // Assert the file contains the right contents
        $this->assertStringContainsString('TestFacade', file_get_contents($this->facadeClassPath));
        $this->assertStringContainsString($this->serviceProviderClass, file_get_contents(config_path('app.php')));
        $this->assertStringContainsString('TestFacade', file_get_contents($this->serviceProviderPath));
    }

    public function test_facade_is_not_created_when_implemented_class_not_specified(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Not enough arguments (missing: "class namespace")');

        // make sure we're starting from a clean state
        if (File::exists($this->facadeClassPath)) {
            unlink($this->facadeClassPath);
        }

        $this->assertFalse(File::exists($this->facadeClassPath));

        // Run the make command
        $this->artisan('make:facade TestFacade');

        // Assert a new file is created
        $this->assertFalse(File::exists($this->facadeClassPath));
        $this->assertFalse(File::exists($this->serviceProviderPath));

        // Assert the file contains the right contents
        $this->assertStringNotContainsString($this->serviceProviderClass, file_get_contents(config_path('app.php')));
    }

    public function test_facade_is_not_created_if_already_exists(): void
    {
        $this->artisan('make:facade TestFacade ' . 'Hmones\\\LaravelFacade\\\Console\\\FacadeMakeCommand')
            ->execute();

        $this->assertTrue(File::exists($this->facadeClassPath));

        $this->artisan('make:facade TestFacade ' . 'Hmones\\\LaravelFacade\\\Console\\\FacadeMakeCommand')
            ->expectsOutput('Facade already exists!')
            ->execute();
    }

    public function test_facade_is_created_if_already_exists_with_force_option(): void
    {
        $this->assertTrue(true);
    }

    public function test_facade_is_not_created_if_name_is_reserved(): void
    {
        $this->assertTrue(true);
    }

    public function test_facade_is_not_created_when_implemented_class_doesnt_exist(): void
    {
        $this->assertTrue(true);
    }

    public function test_updating_config_reflects_on_facade_creation(): void
    {
        $this->assertTrue(true);
    }

    public function tearDown(): void
    {
        File::deleteDirectory(app_path('Facades'));
        File::delete(app_path('Providers/FacadeServiceProvider.php'));
        $appConfig = File::get(config_path('app.php'));
        File::put(config_path('app.php'), preg_replace('/App.*FacadeServiceProvider::class,/', '', $appConfig));
    }
}
