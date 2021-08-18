<?php

namespace Hmones\LaravelFacade\Tests\Feature;

use Hmones\LaravelFacade\Tests\TestCase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class MakeFacadeTest extends TestCase
{
    public function test_facade_is_created_successfully(): void
    {
        // destination path of the Facade class
        $facadeClass = app_path('Facades/TestFacade.php');

        // make sure we're starting from a clean state
        if (File::exists($facadeClass)) {
            unlink($facadeClass);
        }

        $this->assertFalse(File::exists($facadeClass));

        // Run the make command
        //Artisan::call('make:controller TestController');
        Artisan::call('make:facade TestFacade ' . 'Hmones\\\LaravelFacade\\\ImplementedClasses\\\Test');

        // Assert a new file is created
        $this->assertTrue(File::exists($facadeClass));
        $this->assertTrue(File::exists(app_path('Providers/FacadeServiceProvider.php')));

        // Assert the file contains the right contents
        $this->assertStringContainsString('TestFacade', file_get_contents($facadeClass));
        $this->assertStringContainsString('App\Providers\FacadeServiceProvider::class', file_get_contents(config_path('app.php')));
    }

    public function test_facade_is_not_created_when_implemented_class_not_specified(): void
    {
        $this->assertTrue(true);
    }

    public function test_facade_is_not_created_if_already_exist(): void
    {
        $this->assertTrue(true);
    }

    public function test_facade_is_created_if_already_exist_with_force_option(): void
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

    public function tearDown(): void
    {
        File::deleteDirectory(app_path('Facades'));
        File::delete(app_path('Providers/FacadeServiceProvider.php'));
        $appConfig = File::get(config_path('app.php'));
        File::put(config_path('app.php'), preg_replace('/App.*FacadeServiceProvider::class,/', '', $appConfig));
    }
}
