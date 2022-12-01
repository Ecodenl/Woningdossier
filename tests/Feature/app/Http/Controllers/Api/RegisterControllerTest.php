<?php

namespace Tests\Feature\app\Http\Controllers\Api;

use App\Helpers\Arr;
use App\Helpers\ToolQuestionHelper;
use App\Models\Client;
use App\Models\Cooperation;
use App\Models\ToolQuestion;
use App\Models\ToolQuestionType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class RegisterControllerTest extends TestCase
{
    use WithFaker,
        RefreshDatabase;

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

        $answerShorts = [];
        $formData = $this->formData;
        foreach (ToolQuestionHelper::SUPPORTED_API_SHORTS as $short) {
            $toolQuestion = ToolQuestion::factory()->create([
                'short' => $short,
                'validation' => [
                    'required', 'string',
                ],
                'save_in' => null,
                'tool_question_type_id' => ToolQuestionType::findByShort('text')->id,
            ]);

            $formData['tool_questions'][$short] = 'TestAnswer';
            $answerShorts[$short] = $toolQuestion->id;
        }

        $response = $this->post(route('api.v1.cooperation.register.store', compact('cooperation')), $formData);

        $response->assertStatus(201);

        $this->assertDatabaseHas('accounts', ['email' => $this->formData['email']]);

        $this->assertCount(1, $cooperation->users);

        $this->assertDatabaseHas('users', ['allow_access' => 1]);

        $userId = $response->json('user_id');
        $user = User::find($userId);

        // These tool questions will most likely be saved in another table. However, because of testing purposes,
        // they do not have a save_in. This means they should end in the tool_question_answers table.
        foreach ($answerShorts as $toolQuestionShort => $toolQuestionId) {
            $this->assertDatabaseHas('tool_question_answers', [
                'building_id' => $user->building->id,
                'tool_question_id' => $toolQuestionId,
                'answer' => $formData['tool_questions'][$toolQuestionShort],
            ]);
        }
    }

    public function test_restricted_client_cannot_access_cooperation()
    {
        /** @var Cooperation $cooperation */
        Cooperation::factory()->create(['slug' => 'groen-is-gras']);

        $client = Client::factory()->create();
        // now create a token for the client with a cooperation that exists, but the client has no access to
        $client->createToken($client->name . '-token', ['access:groen-is-gras']);

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

        $response = $this->post(route('api.v1.cooperation.register.store', compact('cooperation')), Arr::except($this->formData, ['first_name']));

        $response->assertStatus(422);

        $this->assertDatabaseMissing('accounts', ['email' => $this->formData['email']]);

        $this->assertCount(0, $cooperation->users);
    }
}
