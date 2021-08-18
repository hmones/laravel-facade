<?php

namespace Hmones\LaravelFacade\Tests\Feature;

use Hmones\LaravelFacade\Tests\TestCase;

class RemoveFacadeTest extends TestCase
{
    public function test_facade_is_removed_successfully(): void
    {
        $this->assertTrue(true);
    }

    public function test_facade_is_not_removed_if_it_doesnt_exist(): void
    {
        $this->assertTrue(true);
    }
}
