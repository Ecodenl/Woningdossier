<?php

namespace Tests\Unit\app\Services;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MappingServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_example()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
