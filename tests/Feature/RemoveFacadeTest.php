<?php

namespace Hmones\LaravelFacade\Tests\Feature;

use Hmones\LaravelFacade\Tests\TestCase;
use Illuminate\Support\Facades\File;

class RemoveFacadeTest extends TestCase
{
    const VALID_COMMAND = 'make:facade TestFacade Hmones\\\LaravelFacade\\\Console\\\FacadeMakeCommand';

    public function test_facade_is_removed_successfully(): void
    {
        $this->artisan(self::VALID_COMMAND)
            ->expectsOutput('Publishing Facade Service Provider...')
            ->expectsOutput('Updating Facade Service Provider...')
            ->expectsOutput('Facade created successfully.')
            ->execute();

        $this->assertTrue(File::exists($this->facadeClassPath));

        $this->artisan('remove:facade TestFacade')
            ->expectsOutput('Facade TestFacade purged successfully.')
            ->execute();

        $this->assertFalse(File::exists($this->facadeClassPath));
    }

    public function test_facade_is_not_removed_if_it_doesnt_exist(): void
    {
        $this->assertFalse(File::exists($this->facadeClassPath));

        $this->artisan('remove:facade TestFacade')
            ->expectsOutput('The class TestFacade does not exist.')
            ->execute();
    }

    public function test_facade_is_unregistered_from_service_provider(): void
    {
        $this->artisan(self::VALID_COMMAND)
            ->expectsOutput('Publishing Facade Service Provider...')
            ->expectsOutput('Updating Facade Service Provider...')
            ->expectsOutput('Facade created successfully.')
            ->execute();

        $this->assertTrue(File::exists($this->facadeClassPath));

        $this->artisan('remove:facade TestFacade')
            ->expectsOutput('Facade TestFacade purged successfully.')
            ->execute();

        $this->assertStringContainsString('TestFacade', file_get_contents($this->getProviderPath()));
    }

    public function tearDown(): void
    {
        File::deleteDirectory(app_path('Facades'));
        File::delete($this->getProviderPath());
        $appConfig = File::get(config_path('app.php'));
        File::put(config_path('app.php'), preg_replace('/App.*'.config('laravel-facade.provider.name').'::class,/', '', $appConfig));
        File::delete(config_path('laravel-facade.php'));
    }

}
