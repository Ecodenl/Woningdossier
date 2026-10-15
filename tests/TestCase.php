<?php

namespace Tests;

use App\Jobs\SmartTwin\Out\GetBuildingData;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Bus;
use Spatie\Permission\PermissionRegistrar;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('cache:clear');
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Registration, a reset and every SmartTwin import with advice queue this, and the test
        // queue is sync: without the fake any test that does one of those would call SmartTwin's
        // Building Data API for real. What the job does is tested on BuildingDataService itself.
        Bus::fake([GetBuildingData::class]);
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
