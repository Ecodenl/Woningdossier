<?php

namespace Tests\Feature\app\Http\Controllers\Cooperation\MyAccount;

use App\Helpers\HoomdossierSession;
use App\Helpers\RoleHelper;
use App\Models\Building;
use App\Models\Cooperation;
use App\Models\InputSource;
use App\Models\Role;
use App\Models\User;
use App\Services\BuildingCoachStatusService;
use App\Services\BuildingPermissionService;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * The bottom of 'My account' is the one place a resident detaches a coach, so every attached coach
 * has to be listed there.
 */
final class MyAccountControllerTest extends TestCase
{
    use RefreshDatabase;

    public bool $seed = true;
    public string $seeder = DatabaseSeeder::class;

    private Cooperation $cooperation;
    private Building $building;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();

        $this->cooperation = Cooperation::factory()->create();

        $resident = User::factory()
            ->withAccount()
            ->asResident()
            ->create(['cooperation_id' => $this->cooperation->id, 'allow_access' => false]);

        $this->building = Building::factory()->create(['user_id' => $resident->id]);

        $inputSource = InputSource::findByShort(InputSource::RESIDENT_SHORT);

        $this->actingAs($resident->account);
        HoomdossierSession::setCooperation($this->cooperation);
        HoomdossierSession::setHoomdossierSessions(
            $this->building,
            $inputSource,
            $inputSource,
            Role::findByName(RoleHelper::ROLE_RESIDENT),
        );
    }

    private function makeCoach(): User
    {
        return User::factory()->withAccount()->withBuilding()->asCoach()->create(['cooperation_id' => $this->cooperation->id]);
    }

    public function test_a_coach_attached_without_access_to_the_building_is_listed(): void
    {
        // What a coordinator does while the resident withholds access: attached, but no permission.
        $coach = $this->makeCoach();
        BuildingCoachStatusService::giveAccess($coach, $this->building);

        $response = $this->get(route('cooperation.my-account.index', ['cooperation' => $this->cooperation]));

        $response->assertOk();
        // Escaped like the page escapes it: a generated name can carry an apostrophe.
        $response->assertSee($coach->getFullName());
        $response->assertSee('revoke-access-' . $coach->id, false);
    }

    public function test_a_detached_coach_is_not_listed(): void
    {
        $coach = $this->makeCoach();
        BuildingPermissionService::givePermission($coach, $this->building);
        BuildingCoachStatusService::giveAccess($coach, $this->building);
        BuildingCoachStatusService::revokeAccess($coach, $this->building);

        $response = $this->get(route('cooperation.my-account.index', ['cooperation' => $this->cooperation]));

        $response->assertOk();
        $response->assertDontSee('revoke-access-' . $coach->id, false);
    }
}
