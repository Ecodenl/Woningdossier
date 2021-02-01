<?php

namespace Tests\Feature\app\Http\Controllers\Api;

use App\Models\Client;
use App\Models\Cooperation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class RegisterControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\StatusesTableSeeder::class);
        $this->seed(\RoleTableSeeder::class);
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_valid_data_registers_user()
    {
        /** @var Cooperation $cooperation */
        $cooperation = factory(Cooperation::class)->create();
        /** @var Client $client */
        $client = factory(Client::class)->create();
        $client->createToken($client->name.'-token');

        Sanctum::actingAs($client);

        $data = [
            "email" => $this->faker->email,
            "first_name" => "Demo",
            "last_name" => "Example",
            "postal_code" => "1234AB",
            "number" => "10",
            "house_number_extension" => "",
            "street" => "Teststreet",
            "city" => "Zerocity",
            "phone_number" => null,
            "allow_access" => "on",
        ];

        $response = $this->post(route('api.cooperation.register.store', compact('cooperation')), $data);

        $response->assertStatus(200);

        $this->assertDatabaseHas('accounts', ['email' => $data['email']]);

        $this->assertCount(1, $cooperation->users);
    }

    public function test_invalid_data_returns_422()
    {
        /** @var Cooperation $cooperation */
        $cooperation = factory(Cooperation::class)->create();
        /** @var Client $client */
        $client = factory(Client::class)->create();
        $client->createToken($client->name.'-token');

        Sanctum::actingAs($client);

        $data = [
            "email" => $this->faker->email,
            "first_name" => "Demo",
            "last_name" => "Example",
            "postal_code" => "",
            "number" => "",
            "house_number_extension" => "",
            "street" => "Teststreet",
            "city" => "Zerocity",
            "phone_number" => null,
        ];

        $response = $this->post(route('api.cooperation.register.store', compact('cooperation')), $data);

        $response->assertStatus(422);

        $this->assertDatabaseMissing('accounts', ['email' => $data['email']]);

        $this->assertCount(0, $cooperation->users);
    }
}
