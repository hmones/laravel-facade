<?php

namespace Hmones\LaravelFacade\Tests\Feature;

use Hmones\LaravelFacade\Tests\TestCase;
use Illuminate\Support\Facades\File;
use Symfony\Component\Console\Exception\RuntimeException;

class MakeFacadeTest extends TestCase
{
    const VALID_COMMAND = 'make:facade TestFacade Http/Controllers/Controller.php';

    public function test_facade_is_created_successfully(): void
    {
        if (File::exists($this->facadeClassPath)) {
            unlink($this->facadeClassPath);
        }

        $this->assertFalse(File::exists($this->facadeClassPath));

        $this->artisan(self::VALID_COMMAND)
            ->expectsOutput('Publishing Facade Service Provider...')
            ->expectsOutput('Updating Facade Service Provider...')
            ->expectsOutput('Facade created successfully.')
            ->execute();

        $this->assertFacadeIsRegistered();
    }

    protected function assertFacadeIsRegistered(): void
    {
        $this->assertTrue(File::exists($this->facadeClassPath));
        $this->assertTrue(File::exists($this->getProviderPath()));

        $this->assertStringContainsString('TestFacade', file_get_contents($this->facadeClassPath));
        $this->assertStringContainsString($this->getProviderClass(), file_get_contents(config_path('app.php')));
        $this->assertStringContainsString('TestFacade', file_get_contents($this->getProviderPath()));
    }

    public function test_facade_is_not_created_when_implemented_class_not_specified(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Not enough arguments (missing: "classPath")');

        if (File::exists($this->facadeClassPath)) {
            unlink($this->facadeClassPath);
        }

        $this->assertFalse(File::exists($this->facadeClassPath));

        $this->artisan('make:facade TestFacade')->execute();

        $this->assertFacadeIsNotRegistered();
    }

    protected function assertFacadeIsNotRegistered(): void
    {
        $this->assertFalse(File::exists($this->facadeClassPath));
        $this->assertFalse(File::exists($this->serviceProviderPath));

        $this->assertStringNotContainsString($this->serviceProviderClass, file_get_contents(config_path('app.php')));
    }

    public function test_facade_is_not_created_if_already_exists(): void
    {
        $this->artisan(self::VALID_COMMAND)
            ->execute();

        $this->assertTrue(File::exists($this->facadeClassPath));

        $this->artisan(self::VALID_COMMAND)
            ->expectsOutput('Facade already exists!')
            ->execute();
    }

    public function test_facade_is_not_created_if_name_is_reserved(): void
    {
        if (File::exists($this->facadeClassPath)) {
            unlink($this->facadeClassPath);
        }

        $this->assertFalse(File::exists($this->facadeClassPath));

        $this->artisan(preg_replace('/TestFacade/', 'Class', self::VALID_COMMAND))
            ->expectsOutput('The name \'Class\' is reserved by PHP.')
            ->execute();

        $this->assertFacadeIsNotRegistered();
    }

    public function test_facade_is_not_created_when_implemented_class_doesnt_exist(): void
    {
        if (File::exists($this->facadeClassPath)) {
            unlink($this->facadeClassPath);
        }

        $this->assertFalse(File::exists($this->facadeClassPath));

        $this->artisan('make:facade ExampleFacade NonExisting/Path/ClassName.php')
            ->expectsOutput("The class does not exist in 'app/NonExisting/Path/ClassName.php', please create it first.")
            ->execute();

        $this->assertFacadeIsNotRegistered();
    }

    public function test_updating_config_reflects_on_facade_creation(): void
    {
        $this->artisan('vendor:publish --tag=laravel-facade-config')->execute();

        $configurationPath = config_path('laravel-facade.php');

        $content = preg_replace('/FacadeServiceProvider/', 'CustomServiceProvider', file_get_contents($configurationPath));
        $content = preg_replace('/App\\\Providers/', 'App\\Custom', $content);

        File::put($configurationPath, $content);

        $this->artisan(self::VALID_COMMAND)
            ->expectsOutput('Publishing Facade Service Provider...')
            ->expectsOutput('Updating Facade Service Provider...')
            ->expectsOutput('Facade created successfully.')
            ->execute();

        $this->assertFacadeIsRegistered();
    }

    public function test_package_files_are_published_correctly(): void
    {
        $this->artisan('vendor:publish --tag=laravel-facade-config')->execute();
        $this->assertTrue(File::exists(config_path('laravel-facade.php')));

        $this->artisan('vendor:publish --tag=laravel-facade-provider')->execute();
        $this->assertTrue(File::exists($this->getProviderPath()));
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
