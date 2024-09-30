<?php

namespace Tests\Feature\app\Http\Controllers\Cooperation\Admin;

use App\Events\FillingToolForUserEvent;
use App\Events\ObservingToolForUserEvent;
use App\Helpers\HoomdossierSession;
use App\Helpers\MappingHelper;
use App\Helpers\RoleHelper;
use App\Jobs\CheckBuildingAddress;
use App\Jobs\RefreshRegulationsForBuildingUser;
use App\Models\Account;
use App\Models\Building;
use App\Models\Cooperation;
use App\Models\InputSource;
use App\Models\Municipality;
use App\Models\Role;
use App\Models\Scan;
use App\Models\User;
use App\Services\BuildingCoachStatusService;
use App\Services\BuildingPermissionService;
use App\Services\MappingService;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Tests\MocksLvbag;
use Tests\TestCase;

class ToolControllerTest extends TestCase
{
    use WithFaker,
        MocksLvbag,
        RefreshDatabase;

    public $seed = true;
    public $seeder = DatabaseSeeder::class;

    protected $followRedirects = true;

    public function routeProvider()
    {
        return [
            ['cooperation.admin.tool.fill-for-user'],
            ['cooperation.admin.tool.observe-tool-for-user'],
        ];
    }

    public function routeEventProvider()
    {
        return [
            ['cooperation.admin.tool.fill-for-user', FillingToolForUserEvent::class],
            ['cooperation.admin.tool.observe-tool-for-user', ObservingToolForUserEvent::class],
        ];
    }

    /**
     * @dataProvider routeProvider 
     */
    public function test_accessing_tool_controller_fails_if_no_access(string $routeName): void
    {
        [$resident, $coach] = $this->getFakeUsers();

        $building = $resident->building;
        $cooperation = $resident->cooperation;
        $scan = Scan::findByShort(Scan::QUICK);
        $response = $this->get(
            route($routeName, compact('cooperation', 'building', 'scan'))
        );

        $response->assertStatus(403);
    }

    /**
     * @dataProvider routeEventProvider
     */
    public function test_accessing_tool_controler_dispatches_event(string $routeName, string $event): void
    {
        Event::fake($event);

        [$resident, $coach] = $this->getFakeUsers();

        $building = $resident->building;
        // give the coach permission to the resident his building
        BuildingPermissionService::givePermission($coach, $building);
        BuildingCoachStatusService::giveAccess($coach, $building);

        $cooperation = $resident->cooperation;
        $scan = Scan::findByShort(Scan::QUICK);
        $this->get(
            route($routeName, compact('cooperation', 'building', 'scan'))
        );

        Event::assertDispatched($event);
    }

    /**
     * @dataProvider routeProvider
     */
    public function test_accessing_tool_controller_attempts_to_attach_municipality(string $routeName): void
    {
        Bus::fake(CheckBuildingAddress::class);

        [$resident, $coach] = $this->getFakeUsers();

        $building = $resident->building;
        // give the coach permission to the resident his building
        BuildingPermissionService::givePermission($coach, $building);
        BuildingCoachStatusService::giveAccess($coach, $building);

        $cooperation = $resident->cooperation;
        $scan = Scan::findByShort(Scan::QUICK);
        $this->get(
            route($routeName, compact('cooperation', 'building', 'scan'))
        );

        Bus::assertDispatched(CheckBuildingAddress::class);
    }

    /**
     * @dataProvider routeProvider
     */
    public function test_municipality_attaches_and_regulations_refresh_when_accessing_tool_controller(string $routeName): void
    {
        $fallbackData = [
            'street' => $this->faker->streetName(),
            'number' => $this->faker->numberBetween(3, 22),
            'city' => 'bubba',
            'extension' => 'd',
            'postal_code' => $this->faker->postcode(),
        ];
    
        Bus::fake([RefreshRegulationsForBuildingUser::class]);
        [$resident, $coach] = $this->getFakeUsers();

        $building = $resident->building;
        // give the coach permission to the resident his building
        BuildingPermissionService::givePermission($coach, $building);
        BuildingCoachStatusService::giveAccess($coach, $building);

        $municipality = Municipality::factory()->create();
    
        $fromMunicipalityName = $this->faker->randomElement(['Hatsikidee-Flakkee', 'Hellevoetsluis', 'Haarlem', 'Hollywood']);
        $this->mockLvbagClientAdresUitgebreid($fallbackData)->mockLvbagClientWoonplaats($fromMunicipalityName)->createLvbagMock();
    
        MappingService::init()
            ->from($fromMunicipalityName)
            ->sync([$municipality], MappingHelper::TYPE_BAG_MUNICIPALITY);
    
        $this->assertDatabaseMissing('buildings', [
            'id' => $building->id,
            'municipality_id' => $municipality->id,
        ]);

        $cooperation = $resident->cooperation;
        $scan = Scan::findByShort(Scan::QUICK);
        $this->get(
            route($routeName, compact('cooperation', 'building', 'scan'))
        );

        $this->assertDatabaseHas('buildings', [
            'id' => $building->id,
            'municipality_id' => $municipality->id,
        ]);
        Bus::assertDispatched(RefreshRegulationsForBuildingUser::class);
    }

    /**
     * @dataProvider routeProvider
     */
    public function test_regulations_do_not_refresh_when_accessing_tool_controller_if_no_municipality_attached(string $routeName): void
    {
        Bus::fake([RefreshRegulationsForBuildingUser::class]);
        [$resident, $coach] = $this->getFakeUsers();

        $building = $resident->building;
        // give the coach permission to the resident his building
        BuildingPermissionService::givePermission($coach, $building);
        BuildingCoachStatusService::giveAccess($coach, $building);

        $cooperation = $resident->cooperation;
        $scan = Scan::findByShort(Scan::QUICK);
        $this->get(
            route($routeName, compact('cooperation', 'building', 'scan'))
        );
    
        Bus::assertNotDispatched(RefreshRegulationsForBuildingUser::class);
    }

    /**
     * @dataProvider routeProvider
     */
    public function test_regulations_only_refresh_when_accessing_tool_controller_if_municipality_attached(string $routeName): void
    {
        Bus::fake([RefreshRegulationsForBuildingUser::class]);
        [$resident, $coach] = $this->getFakeUsers();

        $municipality = Municipality::factory()->create();

        $building = $resident->building;
        $building->update([
            'municipality_id' => $municipality->id,
        ]);
        // give the coach permission to the resident his building
        BuildingPermissionService::givePermission($coach, $building);
        BuildingCoachStatusService::giveAccess($coach, $building);

        $cooperation = $resident->cooperation;
        $scan = Scan::findByShort(Scan::QUICK);
        $this->get(
            route($routeName, compact('cooperation', 'building', 'scan'))
        );

        Bus::assertDispatched(RefreshRegulationsForBuildingUser::class);
    }

    private function getFakeUsers(): array
    {
        $userAccount = Account::factory()->create(['password' => Hash::make('secret')]);
        $coachAccount = Account::factory()->create(['password' => Hash::make('secret')]);
        $cooperation = Cooperation::factory()->create();

        $resident = User::factory()
            ->asResident()
            ->create([
                'allow_access' => true,
                'cooperation_id' => $cooperation->id,
                'account_id' => $userAccount->id
            ]);
        $coach = User::factory()
            ->asCoach()
            ->create([
                'allow_access' => true,
                'cooperation_id' => $cooperation->id,
                'account_id' => $coachAccount->id
            ]);

        Building::factory()
            ->create(['user_id' => $resident->id]);
        $coachBuilding = Building::factory()
            ->create(['user_id' => $coach->id]);

        $inputSource = InputSource::findByShort(InputSource::COACH_SHORT);
        $role = Role::findByName(RoleHelper::ROLE_COACH);

        $this->actingAs($coachAccount);
        HoomdossierSession::setHoomdossierSessions($coachBuilding, $inputSource, $inputSource, $role);

        return [$resident, $coach];
    }
}
