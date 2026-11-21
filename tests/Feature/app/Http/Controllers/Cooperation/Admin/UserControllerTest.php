<?php

namespace Tests\Feature\app\Http\Controllers\Cooperation\Admin;

use App\Helpers\HoomdossierSession;
use App\Helpers\RoleHelper;
use App\Models\Account;
use App\Models\Building;
use App\Models\Cooperation;
use App\Models\InputSource;
use App\Models\Role;
use App\Models\ToolQuestion;
use App\Models\User;
use App\Services\CooperationScanService;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Mail;
use Tests\MocksLvbag;
use Tests\TestCase;

/**
 * "Gebruiker toevoegen" asks contract type and monument as the registration page does, in a
 * cooperation with SmartTwin. Decision 2026-10-16-smarttwin-registratie-huur-koop-monument (G4b).
 */
final class UserControllerTest extends TestCase
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

        $this->withoutVite();
        Mail::fake();

        $this->formData = [
            'accounts' => ['email' => $this->faker->email()],
            'users' => ['first_name' => 'Demo', 'last_name' => 'Example', 'phone_number' => null],
            'address' => [
                'postal_code' => '3255MC',
                'number' => '13',
                'extension' => '',
                'street' => 'Boezemweg',
                'city' => 'Oudetonge',
            ],
            'roles' => [Role::findByName(RoleHelper::ROLE_RESIDENT)->id],
            'scans' => ['type' => array_key_first(CooperationScanService::translationMap())],
        ];
        $this->mockLvbagClientAdresUitgebreid($this->formData['address'])->createLvbagMock();
    }

    public function test_create_page_asks_contract_type_and_monument_with_smart_twin(): void
    {
        $cooperation = $this->smartTwinCooperation();
        $this->actAsCooperationAdmin($cooperation);

        $this->get(route('cooperation.admin.users.create', compact('cooperation')))
            ->assertOk()
            ->assertSee('name="tool_questions[building-contract-type]"', false)
            ->assertSee('name="tool_questions[monument]"', false);
    }

    public function test_create_page_does_not_ask_them_without_smart_twin(): void
    {
        $cooperation = Cooperation::factory()->create(['smarttwin_enabled' => false])->fresh();
        $this->actAsCooperationAdmin($cooperation);

        $this->get(route('cooperation.admin.users.create', compact('cooperation')))
            ->assertOk()
            ->assertDontSee('tool_questions[building-contract-type]', false)
            ->assertDontSee('tool_questions[monument]', false);
    }

    public function test_store_requires_them_with_smart_twin(): void
    {
        $cooperation = $this->smartTwinCooperation();
        $this->actAsCooperationAdmin($cooperation);

        $this->post(route('cooperation.admin.users.store', compact('cooperation')), $this->formData)
            ->assertSessionHasErrors(['tool_questions.building-contract-type', 'tool_questions.monument']);

        $this->formData['tool_questions'] = ['building-contract-type' => 'owned', 'monument' => '4'];
        $this->post(route('cooperation.admin.users.store', compact('cooperation')), $this->formData)
            ->assertSessionHasErrors(['tool_questions.building-contract-type', 'tool_questions.monument']);

        $this->assertDatabaseMissing('accounts', ['email' => $this->formData['accounts']['email']]);
    }

    public function test_store_saves_them_for_the_input_source_of_each_role_and_master(): void
    {
        $cooperation = $this->smartTwinCooperation();
        $this->actAsCooperationAdmin($cooperation);
        $this->formData['roles'][] = Role::findByName(RoleHelper::ROLE_COACH)->id;
        $this->formData['tool_questions'] = ['building-contract-type' => 'rented', 'monument' => '2'];

        $this->post(route('cooperation.admin.users.store', compact('cooperation')), $this->formData)
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $building = Account::where('email', $this->formData['accounts']['email'])->first()
            ->users()->forAllCooperations()->first()->building;
        $contractType = ToolQuestion::findByShort('building-contract-type');

        foreach ([InputSource::resident(), InputSource::coach(), InputSource::master()] as $inputSource) {
            $this->assertDatabaseHas('tool_question_answers', [
                'building_id' => $building->id,
                'input_source_id' => $inputSource->id,
                'tool_question_id' => $contractType->id,
                'answer' => 'rented',
            ]);
            $this->assertDatabaseHas('building_features', [
                'building_id' => $building->id,
                'input_source_id' => $inputSource->id,
                'monument' => 2,
            ]);
        }
    }

    public function test_store_does_not_require_them_without_smart_twin(): void
    {
        $cooperation = Cooperation::factory()->create(['smarttwin_enabled' => false])->fresh();
        $this->actAsCooperationAdmin($cooperation);

        $this->post(route('cooperation.admin.users.store', compact('cooperation')), $this->formData)
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $this->assertDatabaseHas('accounts', ['email' => $this->formData['accounts']['email']]);
    }

    /**
     * The super admin adds users to the cooperation they manage, which decides, not their own.
     */
    public function test_super_admin_follows_the_cooperation_to_manage(): void
    {
        $cooperationToManage = $this->smartTwinCooperation();
        $cooperation = Cooperation::factory()->create(['smarttwin_enabled' => false])->fresh();
        $this->actAs($cooperation, RoleHelper::ROLE_SUPER_ADMIN, InputSource::master());
        $parameters = compact('cooperation', 'cooperationToManage');

        $this->get(route('cooperation.admin.super-admin.cooperations.cooperation-to-manage.users.create', $parameters))
            ->assertOk()
            ->assertSee('name="tool_questions[building-contract-type]"', false)
            ->assertSee('name="tool_questions[monument]"', false);

        $this->post(route('cooperation.admin.super-admin.cooperations.cooperation-to-manage.users.store', $parameters), $this->formData)
            ->assertSessionHasErrors(['tool_questions.building-contract-type', 'tool_questions.monument']);
    }

    private function smartTwinCooperation(): Cooperation
    {
        config()->set('hoomdossier.services.smarttwin.enabled', true);

        return Cooperation::factory()->create(['smarttwin_enabled' => true])->fresh();
    }

    private function actAsCooperationAdmin(Cooperation $cooperation): User
    {
        return $this->actAs(
            $cooperation,
            RoleHelper::ROLE_COOPERATION_ADMIN,
            InputSource::findByShort(InputSource::COOPERATION_SHORT),
        );
    }

    private function actAs(Cooperation $cooperation, string $roleName, InputSource $inputSource): User
    {
        $user = User::factory()->create([
            'account_id' => Account::factory()->create()->id,
            'cooperation_id' => $cooperation->id,
        ]);
        $user->assignRole($roleName);
        Building::factory()->create(['user_id' => $user->id]);
        $user->refresh();

        $this->actingAs($user->account);
        HoomdossierSession::setHoomdossierSessions(
            $user->building,
            $inputSource,
            $inputSource,
            Role::findByName($roleName),
        );
        HoomdossierSession::setCooperation($cooperation);

        return $user;
    }
}
