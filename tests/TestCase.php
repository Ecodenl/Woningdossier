<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\PermissionRegistrar;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('cache:clear');
        // TODO: check with colleges, if this is actually fine (https://spatie.be/docs/laravel-permission/v6/upgrading)
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }

    protected function tearDown(): void
    {
        // This is important, as the cache created during testing might conflict with the app, which can cause
        // weird behaviour.
        Artisan::call('cache:clear');
        // TODO: check with colleges, if this is actually fine (https://spatie.be/docs/laravel-permission/v6/upgrading)
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        parent::tearDown();
    }
}
