<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Artisan;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('cache:clear');
    }

    protected function tearDown(): void
    {
        // This is important, as the cache created during testing might conflict with the app, which can cause
        // weird behaviour.
        Artisan::call('cache:clear');
        parent::tearDown();
    }
}
