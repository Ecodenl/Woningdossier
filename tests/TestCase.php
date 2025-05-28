<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Spatie\Permission\PermissionRegistrar;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('cache:clear');
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }

    protected function tearDown(): void
    {
        // This is important, as the cache created during testing might conflict with the app, which can cause
        // weird behaviour.
        $this->artisan('cache:clear');
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        parent::tearDown();
    }
}
