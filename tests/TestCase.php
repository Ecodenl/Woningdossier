<?php

namespace Tests;

use Database\Seeders\ToolQuestionTypesTableSeeder;
use Database\Seeders\StatusesTableSeeder;
use Database\Seeders\RoleTableSeeder;
use Database\Seeders\InputSourcesTableSeeder;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Artisan;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed data that should ALWAYS be present
        Artisan::call('db:seed', ['--class' => ToolQuestionTypesTableSeeder::class, '--force' => true]);
        Artisan::call('db:seed', ['--class' => InputSourcesTableSeeder::class, '--force' => true]);
        Artisan::call('db:seed', ['--class' => RoleTableSeeder::class, '--force' => true]);
        Artisan::call('db:seed', ['--class' => StatusesTableSeeder::class, '--force' => true]);

        // Ensure we clear cache (findByShort could be troublesome)
        Artisan::call('cache:clear');
    }
}
