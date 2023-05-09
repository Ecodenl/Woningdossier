<?php

namespace Tests\Feature\app\Http\Controllers\Api;

use App\Helpers\Arr;
use App\Helpers\ToolQuestionHelper;
use App\Models\Client;
use App\Models\Cooperation;
use App\Models\InputSource;
use App\Models\ToolQuestion;
use App\Models\ToolQuestionType;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\Sanctum;
use Tests\MocksLvbag;
use Tests\TestCase;

class RegisterControllerTest extends TestCase
{
    use WithFaker,
        RefreshDatabase,
        MocksLvbag;

    public $seed = true;
    public $seeder = DatabaseSeeder::class;

    private array $formData;

    protected function setUp(): void
    {
        parent::setUp();

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
        ];
        $this->mockLvbagClientAdresUitgebreid($this->formData)->createLvbagMock();
    }

    public function test_valid_data_registers_new_account()
    {
        /** @var Cooperation $cooperation */
        $cooperation = Cooperation::factory()->create();
        /** @var Client $client */
        $client = Client::factory()->create();

        Sanctum::actingAs($client, ['*']);

        $response = $this->post(route('api.v1.cooperation.register.store', compact('cooperation')), $this->formData);

        $response->assertStatus(201);

        $this->assertDatabaseHas('accounts', ['email' => $this->formData['email']]);

        $this->assertCount(1, $cooperation->users);

        $this->assertDatabaseHas('users', ['allow_access' => 1]);
    }

    public function test_valid_data_with_tool_question_answers_registers_new_account()
    {
        /** @var Cooperation $cooperation */
        $cooperation = Cooperation::factory()->create();
        /** @var Client $client */
        $client = Client::factory()->create();

        Sanctum::actingAs($client, ['*']);

        $formData = $this->formData;

        $formData['tool_questions']['amount-gas'] = '2100';

        $response = $this->post(route('api.v1.cooperation.register.store', compact('cooperation')), $formData);

        $response->assertStatus(201);

        $this->assertDatabaseHas('accounts', ['email' => $this->formData['email']]);
        $account = DB::table('accounts')->where('email', $this->formData['email'])->first();
        $this->assertDatabaseHas('users', ['account_id' => $account->id, 'allow_access' => 1]);
        $user = DB::table('users')->where('account_id', $account->id)->first();
        $this->assertDatabaseHas('buildings', ['user_id' => $user->id]);


        $userId = $response->json('user_id');
        $user = User::find($userId);

        $this->assertDatabaseHas('user_energy_habits', [
            'amount_gas' => $formData['tool_questions']['amount-gas'],
            'user_id' => $user->id,
            'input_source_id' => InputSource::findByShort('resident')->id
        ]);
    }

    public function test_restricted_client_cannot_access_cooperation()
    {
        /** @var Cooperation $cooperation */
        Cooperation::factory()->create(['slug' => 'groen-is-gras']);

        $client = Client::factory()->create();
        // now create a token for the client with a cooperation that exists, but the client has no access to
        $client->createToken($client->name.'-token', ['access:groen-is-gras']);

        /** @var Client $client */
        Sanctum::actingAs($client);

        // now do a post request to the cooperation, the client should have no access to this cooperation.
        $cooperation = Cooperation::factory()->create(['slug' => 'co2-neutraal']);
        $response = $this->post(route('api.v1.cooperation.register.store', compact('cooperation')));
        $response->assertForbidden();

    }

    public function test_restricted_client_can_access_cooperation()
    {
        /** @var Cooperation $cooperation */
        Cooperation::factory()->create(['slug' => 'groen-is-gras']);
        $cooperation = Cooperation::factory()->create(['slug' => 'co2-neutraal']);

        // now create a token for the client with a cooperation that exists, but the client has no access to
        /** @var Client $client */
        $client = Client::factory()->create();
        Sanctum::actingAs($client, ['access:co2-neutraal']);

        // now do a post request to the cooperation, the client should have no access to this cooperation.
        $response = $this->get(route('api.v1.cooperation.index', compact('cooperation')));

        $response->assertOk();
    }

    public function test_existing_account_will_register_user_on_other_cooperation()
    {
        /** @var Client $client */
        $client = Client::factory()->create();

        Sanctum::actingAs($client, ['*']);

        // first create the initial account and user on the first cooperation.
        $cooperation = Cooperation::factory()->create(['slug' => 'groen-is-gras']);
        $response = $this->post(route('api.v1.cooperation.register.store', compact('cooperation')), $this->formData);
        $response->assertStatus(201);

        // now we do it again, this time it should create another user for the existing account. But for another cooperation.
        $cooperation = Cooperation::factory()->create(['slug' => 'meteropnull']);
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
        $cooperation = Cooperation::factory()->create();
        /** @var Client $client */
        $client = Client::factory()->create();
        Sanctum::actingAs($client, ['*']);

        $response = $this->post(route('api.v1.cooperation.register.store', compact('cooperation')),
            Arr::except($this->formData, ['first_name']));

        $response->assertStatus(422);

        $this->assertDatabaseMissing('accounts', ['email' => $this->formData['email']]);

        $this->assertCount(0, $cooperation->users);
    }
}
