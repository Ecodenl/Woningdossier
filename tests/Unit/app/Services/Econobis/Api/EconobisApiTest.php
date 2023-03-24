<?php

namespace Tests\Unit\app\Services\Econobis\Api;

use App\Services\Econobis\Api\Client;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Mockery\MockInterface;
use Tests\TestCase;

class EconobisApiTest extends TestCase
{
    use WithFaker;

    //public $seed = true;
    // public $seeder = DatabaseSeeder::class;

    protected function setUp(): void
    {
        parent::setUp();
        Artisan::call('cache:clear');
    }

    public function test_on_production_it_only_uses_cooperation_defined_api_endpoint_values()
    {
        dd(env('APP_ENV'));
    }
}