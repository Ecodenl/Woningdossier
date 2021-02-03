<?php

namespace Tests\Feature\app\Http\Controllers\Api;

use App\Helpers\Arr;
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

    private array $formData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\StatusesTableSeeder::class);
        $this->seed(\RoleTableSeeder::class);

        $this->formData = [
            "email" => $this->faker->email,
            "first_name" => "Demo",
            "last_name" => "Example",
            "postal_code" => "1234AB",
            "number" => "10",
            'extra' => [
                'contact_id' => $this->faker->numberBetween(20, 10443042),
            ],
            "house_number_extension" => "",
            "street" => "Teststreet",
            "city" => "Zerocity",
            "phone_number" => null,
            "allow_access" => "on",
        ];
    }

    public function test_valid_data_registers_new_account()
    {
        /** @var Cooperation $cooperation */
        $cooperation = factory(Cooperation::class)->create();
        /** @var Client $client */
        $client = factory(Client::class)->create();

        Sanctum::actingAs($client, ['*']);

        $response = $this->post(route('api.v1.cooperation.register.store', compact('cooperation')), $this->formData);

        $response->assertStatus(201);

        $this->assertDatabaseHas('accounts', ['email' => $this->formData['email']]);

        $this->assertCount(1, $cooperation->users);
    }

    public function test_restricted_client_cannot_access_cooperation()
    {
        /** @var Cooperation $cooperation */
        factory(Cooperation::class)->create(['slug' => 'groen-is-gras']);

        $client = factory(Client::class)->create();
        // now create a token for the client with a cooperation that exists, but the client has no access to
        $client->createToken($client->name . '-token', ['access:groen-is-gras']);

        /** @var Client $client */
        Sanctum::actingAs($client);

        // now do a post request to the cooperation, the client should have no access to this cooperation.
        $cooperation = factory(Cooperation::class)->create(['slug' => 'co2-neutraal']);
        $response = $this->post(route('api.v1.cooperation.register.store', compact('cooperation')));
        $response->assertForbidden();

    }

    public function test_restricted_client_can_access_cooperation()
    {
        /** @var Cooperation $cooperation */
        factory(Cooperation::class)->create(['slug' => 'groen-is-gras']);
        $cooperation = factory(Cooperation::class)->create(['slug' => 'co2-neutraal']);

        // now create a token for the client with a cooperation that exists, but the client has no access to
        /** @var Client $client */
        $client = factory(Client::class)->create();
        Sanctum::actingAs($client, ['access:co2-neutraal']);

        // now do a post request to the cooperation, the client should have no access to this cooperation.
        $response = $this->get(route('api.v1.cooperation.index', compact('cooperation')));

        $response->assertOk();

    }

    public function test_existing_account_will_register_user_on_other_cooperation()
    {
        /** @var Client $client */
        $client = factory(Client::class)->create();

        Sanctum::actingAs($client, ['*']);

        // first create the initial account and user on the first cooperation.
        $cooperation = factory(Cooperation::class)->create(['slug' => 'groen-is-gras']);
        $response = $this->post(route('api.v1.cooperation.register.store', compact('cooperation')), $this->formData);
        $response->assertStatus(201);

        // now we do it again, this time it should create another user for the existing account. But for another cooperation.
        $cooperation = factory(Cooperation::class)->create(['slug' => 'meteropnull']);
        $response = $this->post(route('api.v1.cooperation.register.store', compact('cooperation')), $this->formData);
        $response->assertStatus(201);


        // there should be 1 account for this email, with 2 users.
        $accounts = DB::table('accounts')->where('email', $this->formData['email'])->get();
        $this->assertTrue($accounts->count() === 1);

        // we already asserted there is only 1 account, so we can safely retrieve the first one.
        // now make sure there are 2 users for 1 account.
        $account = $accounts->first();
        $this->assertCount(2, DB::table('users')->where('account_id', $account->id)->get());
        $this->assertDatabaseHas('accounts', ['email' => $this->formData['email']]);

    }

    public function test_invalid_data_returns_422()
    {
        /** @var Cooperation $cooperation */
        $cooperation = factory(Cooperation::class)->create();
        /** @var Client $client */
        $client = factory(Client::class)->create();
        Sanctum::actingAs($client, ['*']);

        $response = $this->post(route('api.v1.cooperation.register.store', compact('cooperation')), Arr::except($this->formData, ['first_name']));

        $response->assertStatus(422);

        $this->assertDatabaseMissing('accounts', ['email' => $this->formData['email']]);

        $this->assertCount(0, $cooperation->users);
    }
}
