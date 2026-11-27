<?php

namespace Tests\Feature\app\Http\Controllers\Cooperation\Auth;

use App\Helpers\Arr;
use App\Helpers\ToolQuestionHelper;
use App\Models\Account;
use App\Models\Building;
use App\Models\Client;
use App\Models\Cooperation;
use App\Models\InputSource;
use App\Models\ToolQuestion;
use App\Models\ToolQuestionType;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Dflydev\DotAccessData\Data;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\Sanctum;
use OwenIt\Auditing\Drivers\Database;
use Tests\MocksLvbag;
use Tests\TestCase;

final class RegisteredUserControllerTest extends TestCase
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
            "email" => $this->faker->email(),
            "first_name" => "Demo",
            "last_name" => "Example",
            'address' => [
                "postal_code" => "3255MC",
                "number" => "13",
                "extension" => "",
                "street" => "Boezemweg",
                "city" => "Oudetonge",
            ],
            "phone_number" => null,
            'allow_access' => true,
            'password' => $this->faker->password('8')
        ];
        $this->mockLvbagClientAdresUitgebreid($this->formData['address'])->createLvbagMock();
    }

    public function test_valid_data_registers_new_account(): void
    {
        /** @var Cooperation $cooperation */
        $cooperation = Cooperation::factory()->create();

        /** @var Client $client */
        $this->formData['password_confirmation'] = $this->formData['password'];
        $response = $this->post(route('cooperation.register.store', compact('cooperation')), $this->formData);
        $response->assertRedirectToRoute('cooperation.auth.verification.notice', compact('cooperation'));

        $this->assertDatabaseHas('accounts', ['email' => $this->formData['email']]);
        $account = DB::table('accounts')->where('email', $this->formData['email'])->first();
        $this->assertDatabaseHas('users', ['account_id' => $account->id, 'allow_access' => 1]);
        $user = DB::table('users')->where('account_id', $account->id)->first();
        $this->assertDatabaseHas('buildings', ['user_id' => $user->id]);
    }

    public function test_register_page_asks_contract_type_and_monument_with_smart_twin(): void
    {
        $this->withoutVite();
        $cooperation = $this->smartTwinCooperation();

        $this->get(route('cooperation.register', compact('cooperation')))
            ->assertOk()
            ->assertSee('name="tool_questions[building-contract-type]"', false)
            ->assertSee('name="tool_questions[monument]"', false)
            ->assertSee(__('auth.register.form.tool-questions.building-contract-type'))
            ->assertSee(__('auth.register.form.tool-questions.monument'))
            ->assertSee('Particuliere huur')
            ->assertSee('Beschermd stads- of dorpsgezicht');
    }

    public function test_register_page_does_not_ask_them_without_smart_twin(): void
    {
        $this->withoutVite();
        $cooperation = Cooperation::factory()->create(['smarttwin_enabled' => false]);

        $this->get(route('cooperation.register', compact('cooperation')))
            ->assertOk()
            ->assertDontSee('tool_questions[building-contract-type]', false)
            ->assertDontSee('tool_questions[monument]', false);
    }

    public function test_smart_twin_registration_requires_contract_type_and_monument(): void
    {
        $cooperation = $this->smartTwinCooperation();
        $this->formData['password_confirmation'] = $this->formData['password'];

        $this->post(route('cooperation.register.store', compact('cooperation')), $this->formData)
            ->assertSessionHasErrors(['tool_questions.building-contract-type', 'tool_questions.monument']);

        $this->formData['tool_questions'] = ['building-contract-type' => 'owned', 'monument' => '4'];
        $this->post(route('cooperation.register.store', compact('cooperation')), $this->formData)
            ->assertSessionHasErrors(['tool_questions.building-contract-type', 'tool_questions.monument']);

        $this->assertDatabaseMissing('accounts', ['email' => $this->formData['email']]);
    }

    public function test_smart_twin_registration_saves_contract_type_and_monument_for_resident_and_master(): void
    {
        $cooperation = $this->smartTwinCooperation();
        $this->formData['password_confirmation'] = $this->formData['password'];
        // "Onbekend" is 0: required must let it through.
        $this->formData['tool_questions'] = ['building-contract-type' => 'rented-private', 'monument' => '0'];

        $this->post(route('cooperation.register.store', compact('cooperation')), $this->formData)
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $building = Account::where('email', $this->formData['email'])->first()->users()->forAllCooperations()->first()->building;
        $this->assertAnswers($building, [InputSource::resident(), InputSource::master()], 'rented-private', 0);
    }

    public function test_member_without_building_answers_them_with_the_address(): void
    {
        $cooperation = $this->smartTwinCooperation();
        $this->formData['password_confirmation'] = $this->formData['password'];
        $this->formData['tool_questions'] = ['building-contract-type' => 'bought', 'monument' => '1'];

        $this->post(route('cooperation.register.store', compact('cooperation')), $this->formData)
            ->assertSessionHasNoErrors()
            ->assertRedirect();
        $user = Account::where('email', $this->formData['email'])->first()->users()->forAllCooperations()->first();
        $user->building->delete();
        // Registering logs in; the register page is for guests.
        $this->app['auth']->guard()->logout();

        // Only the address is asked now, and the two questions with it.
        $address = ['email' => $this->formData['email'], 'address' => $this->formData['address']];
        $this->post(route('cooperation.register.store', compact('cooperation')), $address)
            ->assertSessionHasErrors(['tool_questions.building-contract-type', 'tool_questions.monument']);

        $address['tool_questions'] = ['building-contract-type' => 'rented', 'monument' => '3'];
        $this->post(route('cooperation.register.store', compact('cooperation')), $address)
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $this->assertAnswers($user->fresh()->building, [InputSource::resident(), InputSource::master()], 'rented', 3);
    }

    public function test_registration_without_smart_twin_ignores_them(): void
    {
        $cooperation = Cooperation::factory()->create(['smarttwin_enabled' => false]);
        $this->formData['password_confirmation'] = $this->formData['password'];
        $this->formData['tool_questions'] = ['building-contract-type' => 'bought', 'monument' => '1'];

        $this->post(route('cooperation.register.store', compact('cooperation')), $this->formData)
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $building = Account::where('email', $this->formData['email'])->first()->users()->forAllCooperations()->first()->building;
        $this->assertDatabaseMissing('tool_question_answers', [
            'building_id' => $building->id,
            'tool_question_id' => ToolQuestion::findByShort('building-contract-type')->id,
        ]);
    }

    private function smartTwinCooperation(): Cooperation
    {
        config()->set('hoomdossier.services.smarttwin.enabled', true);

        return Cooperation::factory()->create(['smarttwin_enabled' => true])->fresh();
    }

    /**
     * @param  InputSource[]  $inputSources
     */
    private function assertAnswers(Building $building, array $inputSources, string $contractType, int $monument): void
    {
        $contractTypeQuestion = ToolQuestion::findByShort('building-contract-type');

        foreach ($inputSources as $inputSource) {
            $this->assertDatabaseHas('tool_question_answers', [
                'building_id' => $building->id,
                'input_source_id' => $inputSource->id,
                'tool_question_id' => $contractTypeQuestion->id,
                'answer' => $contractType,
            ]);
            $this->assertDatabaseHas('building_features', [
                'building_id' => $building->id,
                'input_source_id' => $inputSource->id,
                'monument' => $monument,
            ]);
        }
    }
}
