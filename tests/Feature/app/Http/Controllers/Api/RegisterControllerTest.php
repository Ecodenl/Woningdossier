<?php

namespace Tests\Feature\app\Http\Controllers\Api;

use App\Enums\Country;
use App\Helpers\Arr;
use App\Helpers\Models\BuildingSettingHelper;
use App\Models\Client;
use App\Models\Cooperation;
use App\Models\InputSource;
use App\Models\Scan;
use App\Models\ToolQuestion;
use App\Models\User;
use App\Services\CooperationScanService;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Illuminate\Testing\TestResponse;
use Laravel\Sanctum\Sanctum;
use Tests\MocksLvbag;
use Tests\TestCase;

final class RegisterControllerTest extends TestCase
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
            Country::NL->value => [
                "email" => $this->faker->email(),
                "first_name" => "Demo",
                "last_name" => "Example",
                "postal_code" => "1234AB",
                "number" => "10",
                "house_number_extension" => "",
                'extra' => [
                    'contact_id' => $this->faker->numberBetween(20, 10443042),
                ],
                "street" => "Teststreet",
                "city" => "Zerocity",
                "phone_number" => '+31611223344',
            ],
            Country::BE->value => [
                "email" => $this->faker->email(),
                "first_name" => "Demo",
                "last_name" => "Example",
                'postal_code' => '1000',
                'number' => '10',
                "house_number_extension" => "",
                'extra' => [
                    'contact_id' => $this->faker->numberBetween(20, 10443042),
                ],
                "street" => "Teststreet",
                "city" => "Zerocity",
                "phone_number" => '+32460123456',
            ],
        ];
        // Only create mock for NL, since BE doesn't support it and will only trigger on NL data.
        $this->mockLvbagClientAdresUitgebreid($this->formData[Country::NL->value])->createLvbagMock();
    }

    public function test_valid_data_registers_new_account(): void
    {
        foreach ($this->formData as $country => $formData) {
            /** @var Cooperation $cooperation */
            $cooperation = Cooperation::factory()->create(compact('country'));
            /** @var Client $client */
            $client = Client::factory()->create();

            Sanctum::actingAs($client, ['*']);

            $response = $this->post(route('api.v1.cooperation.register.store', compact('cooperation')), $formData);

            $response->assertStatus(201);

            $this->assertDatabaseHas('accounts', ['email' => $formData['email']]);

            $this->assertCount(1, $cooperation->users);

            $this->assertDatabaseHas('users', ['allow_access' => 1]);
        }
    }

    public function test_valid_data_with_tool_question_answers_registers_new_account(): void
    {
        foreach ($this->formData as $country => $formData) {
            /** @var Cooperation $cooperation */
            $cooperation = Cooperation::factory()->create(compact('country'));
            /** @var Client $client */
            $client = Client::factory()->create();

            Sanctum::actingAs($client, ['*']);

            $formData['tool_questions']['amount-gas'] = '2100';

            $response = $this->post(route('api.v1.cooperation.register.store', compact('cooperation')), $formData);

            $response->assertStatus(201);

            $this->assertDatabaseHas('accounts', ['email' => $formData['email']]);
            $account = DB::table('accounts')->where('email', $formData['email'])->first();
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
    }

    public function test_restricted_client_cannot_access_cooperation(): void
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

    public function test_restricted_client_can_access_cooperation(): void
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

    public function test_existing_account_will_register_user_on_other_cooperation(): void
    {
        foreach ($this->formData as $country => $formData) {
            /** @var Client $client */
            $client = Client::factory()->create();

            Sanctum::actingAs($client, ['*']);

            // first create the initial account and user on the first cooperation.
            $cooperation = Cooperation::factory()->create(['country' => $country, 'slug' => "groen-is-gras-{$country}"]);
            $response = $this->post(route('api.v1.cooperation.register.store', compact('cooperation')),
                $formData);

            $response->assertStatus(201);

            // now we do it again, this time it should create another user for the existing account. But for another cooperation.
            $cooperation = Cooperation::factory()->create(['country' => $country, 'slug' => "meteropnull-{$country}"]);
            $response = $this->post(route('api.v1.cooperation.register.store', compact('cooperation')),
                $formData);

            $response->assertStatus(201);


            // there should be 1 account for this email, with 2 users.
            $accounts = DB::table('accounts')->where('email', $formData['email'])->get();
            $this->assertTrue($accounts->count() === 1);

            // we already asserted there is only 1 account, so we can safely retrieve the first one.
            // now make sure there are 2 users for 1 account.
            $account = $accounts->first();
            $this->assertCount(2, DB::table('users')->where('account_id', $account->id)->get());
            $this->assertDatabaseHas('accounts', ['email' => $formData['email']]);
        }
    }

    public function test_invalid_data_returns_422(): void
    {
        foreach ($this->formData as $country => $formData) {
            /** @var Cooperation $cooperation */
            $cooperation = Cooperation::factory()->create(compact('country'));
            /** @var Client $client */
            $client = Client::factory()->create();
            Sanctum::actingAs($client, ['*']);

            $response = $this->post(route('api.v1.cooperation.register.store', compact('cooperation')),
                Arr::except($formData, ['first_name']));

            $response->assertStatus(422);

            $this->assertDatabaseMissing('accounts', ['email' => $formData['email']]);

            $this->assertCount(0, $cooperation->users);
        }
    }

    public function test_building_follows_cooperation_scans_without_scan_settings(): void
    {
        $response = $this->registerWithScans(Scan::QUICK, null);

        $response->assertStatus(201);
        $this->assertDatabaseMissing('building_settings', ['building_id' => $this->buildingIdFor($response)]);
    }

    public function test_scan_type_overrides_cooperation_scans(): void
    {
        $response = $this->registerWithScans(Scan::QUICK, ['type' => Scan::LITE]);

        $response->assertStatus(201);
        $buildingId = $this->buildingIdFor($response);
        $this->assertDatabaseHas('building_settings', [
            'building_id' => $buildingId,
            'short' => BuildingSettingHelper::SHORT_SCAN_ENABLED_LITE_SCAN,
            'value' => '1',
        ]);
        $this->assertDatabaseHas('building_settings', [
            'building_id' => $buildingId,
            'short' => BuildingSettingHelper::SHORT_SCAN_ENABLED_QUICK_SCAN,
            'value' => '0',
        ]);
    }

    public function test_scan_type_matching_cooperation_scans_stores_no_override(): void
    {
        $response = $this->registerWithScans('both-scans', ['type' => 'both-scans']);

        $response->assertStatus(201);
        $this->assertDatabaseMissing('building_settings', ['building_id' => $this->buildingIdFor($response)]);
    }

    public function test_invalid_scan_type_returns_422(): void
    {
        $response = $this->registerWithScans(Scan::QUICK, ['type' => Scan::EXPERT]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('scans.type', null);
    }

    public function test_small_measures_override_is_stored(): void
    {
        $response = $this->registerWithScans(Scan::QUICK, ['type' => 'both-scans', 'small_measures_enabled' => false]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('building_settings', [
            'building_id' => $this->buildingIdFor($response),
            'short' => BuildingSettingHelper::SHORT_SMALL_MEASURES_ENABLED_QUICK_SCAN,
            'value' => '0',
        ]);
    }

    public function test_small_measures_matching_cooperation_stores_no_override(): void
    {
        // Small measures are enabled for a cooperation by default.
        $response = $this->registerWithScans(Scan::QUICK, ['small_measures_enabled' => true]);

        $response->assertStatus(201);
        $this->assertDatabaseMissing('building_settings', ['building_id' => $this->buildingIdFor($response)]);
    }

    public function test_small_measures_without_quick_scan_type_returns_422(): void
    {
        $response = $this->registerWithScans(Scan::QUICK, ['type' => Scan::LITE, 'small_measures_enabled' => false]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('scans.small_measures_enabled', null);
    }

    public function test_small_measures_without_quick_scan_for_cooperation_returns_422(): void
    {
        $response = $this->registerWithScans(Scan::LITE, ['small_measures_enabled' => false]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('scans.small_measures_enabled', null);
    }

    public function test_contract_type_and_monument_are_saved_for_resident_and_master(): void
    {
        // Optional in any cooperation: an API client need not know whether SmartTwin is on.
        $response = $this->registerWithToolQuestions(['building-contract-type' => 'rented-private', 'monument' => 0]);

        $response->assertStatus(201);
        $buildingId = $this->buildingIdFor($response);
        $contractType = ToolQuestion::findByShort('building-contract-type');

        foreach ([InputSource::resident(), InputSource::master()] as $inputSource) {
            $this->assertDatabaseHas('tool_question_answers', [
                'building_id' => $buildingId,
                'input_source_id' => $inputSource->id,
                'tool_question_id' => $contractType->id,
                'answer' => 'rented-private',
            ]);
            $this->assertDatabaseHas('building_features', [
                'building_id' => $buildingId,
                'input_source_id' => $inputSource->id,
                'monument' => 0,
            ]);
        }
    }

    public function test_contract_type_and_monument_only_accept_their_own_options(): void
    {
        // "2" is an option of resident-count: the question's own validation (exists on any custom value) let it through.
        $response = $this->registerWithToolQuestions(['building-contract-type' => '2', 'monument' => 4]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['tool_questions.building-contract-type', 'tool_questions.monument'], null);
    }

    public function test_contract_type_and_monument_may_be_null(): void
    {
        $response = $this->registerWithToolQuestions(['building-contract-type' => null, 'monument' => null]);

        $response->assertStatus(201);
        $this->assertDatabaseMissing('tool_question_answers', [
            'building_id' => $this->buildingIdFor($response),
            'tool_question_id' => ToolQuestion::findByShort('building-contract-type')->id,
        ]);
    }

    // TODO: Create test for duplicate contact ID in the same cooperation

    private function registerWithToolQuestions(array $toolQuestions): TestResponse
    {
        /** @var Cooperation $cooperation */
        $cooperation = Cooperation::factory()->create(['country' => Country::NL->value]);

        Sanctum::actingAs(Client::factory()->create(), ['*']);

        $formData = $this->formData[Country::NL->value];
        $formData['tool_questions'] = $toolQuestions;

        return $this->postJson(route('api.v1.cooperation.register.store', compact('cooperation')), $formData);
    }

    private function registerWithScans(string $cooperationScanType, ?array $scans): TestResponse
    {
        /** @var Cooperation $cooperation */
        $cooperation = Cooperation::factory()->create(['country' => Country::NL->value]);
        CooperationScanService::init($cooperation)->syncScan($cooperationScanType);

        Sanctum::actingAs(Client::factory()->create(), ['*']);

        $formData = $this->formData[Country::NL->value];
        if (! is_null($scans)) {
            $formData['scans'] = $scans;
        }

        return $this->postJson(route('api.v1.cooperation.register.store', compact('cooperation')), $formData);
    }

    private function buildingIdFor(TestResponse $response): int
    {
        return User::find($response->json('user_id'))->building->id;
    }
}
