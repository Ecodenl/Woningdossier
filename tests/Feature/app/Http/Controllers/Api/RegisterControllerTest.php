<?php

namespace Tests\Feature\app\Http\Controllers\Api;

use App\Models\Client;
use App\Models\Cooperation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
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

    public function test_valid_data_registers_new_account()
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

    public function test_existing_account_will_register_user_on_other_cooperation()
    {
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

        // first create the initial account and user on the first cooperation.
        $cooperation = factory(Cooperation::class)->create(['slug' => 'groen-is-gras']);
        $response = $this->post(route('api.cooperation.register.store', compact('cooperation')), $data);
        $response->assertStatus(200);

        // now we do it again, this time it should create another user for the existing account. But for another cooperation.
        $cooperation = factory(Cooperation::class)->create(['slug' => 'meteropnull']);
        $response = $this->post(route('api.cooperation.register.store', compact('cooperation')), $data);
        $response->assertStatus(200);


        // there should be 1 account for this email, with 2 users.
        $accounts = DB::table('accounts')->where('email', $data['email'])->get();
        $this->assertTrue($accounts->count() === 1);

        // we already asserted there is only 1 account, so we can safely retrieve the first one.
        // now make sure there are 2 users for 1 account.
        $account = $accounts->first();
        $this->assertCount(2, DB::table('users')->where('account_id', $account->id)->get());
        $this->assertDatabaseHas('accounts', ['email' => $data['email']]);

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
