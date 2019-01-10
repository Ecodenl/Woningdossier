<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testBasicTest()
    {
    	$this->markTestSkipped("Database on scrutinizer not working?");
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
