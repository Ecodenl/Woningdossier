<?php

namespace Tests\Feature\app\Jobs\SmartTwin\Out;

use App\Helpers\RoleHelper;
use App\Jobs\AttachEnergyLabel;
use App\Jobs\CheckBuildingAddress;
use App\Jobs\SmartTwin\Out\GetBuildingData;
use App\Models\Building;
use App\Models\BuildingFeature;
use App\Models\Cooperation;
use App\Models\InputSource;
use App\Models\User;
use App\Services\BuildingAddressService;
use App\Services\Lvbag\BagService;
use App\Services\Lvbag\Payloads\AddressExpanded;
use App\Services\SmartTwin\BuildingDataService;
use App\Services\UserService;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Mockery;
use Tests\TestCase;

/**
 * When SmartTwin's Building Data is fetched: in a cooperation with SmartTwin at registration, at a
 * reset and at an address change, in place of EP-Online and of BAG's build year and surface. BAG
 * still does the address and the municipality (decision 2026-10-15-smarttwin-woninggegevens-
 * building-data, B9, B9a, B13). A cooperation without SmartTwin keeps BAG and EP-Online.
 */
final class GetBuildingDataTest extends TestCase
{
    use RefreshDatabase;

    public $seed = true;
    public $seeder = DatabaseSeeder::class;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('hoomdossier.services.smarttwin.enabled', true);
        Bus::fake([GetBuildingData::class, AttachEnergyLabel::class, CheckBuildingAddress::class]);
    }

    private function cooperation(bool $smartTwin): Cooperation
    {
        // Fresh: the factory leaves `country` an enum, which getCountry() does not take until it has
        // been through the database.
        return Cooperation::factory()->create(['smarttwin_enabled' => $smartTwin])->fresh();
    }

    private function register(Cooperation $cooperation): User
    {
        return UserService::register($cooperation, [RoleHelper::ROLE_RESIDENT], [
            'email'      => 'bewoner@example.org',
            'password'   => 'secret-password',
            'first_name' => 'Test',
            'last_name'  => 'Bewoner',
            'address'    => ['postal_code' => '5224HD', 'number' => '1', 'extension' => '', 'street' => 'Teststraat', 'city' => "'s-Hertogenbosch"],
        ]);
    }

    private function fakeBag(): void
    {
        $bag = Mockery::mock(BagService::class);
        $bag->shouldReceive('addressExpanded')->andReturn(new AddressExpanded([
            'endpoint_failure'              => false,
            'oorspronkelijkBouwjaar'        => [1930],
            'oppervlakte'                   => 99,
            'nummeraanduidingIdentificatie' => '0796200000000001',
            'woonplaatsIdentificatie'       => '1234',
        ]));
        $this->app->instance(BagService::class, $bag);
    }

    public function test_registration_with_smart_twin_fetches_building_data_instead_of_ep_online(): void
    {
        $user = $this->register($this->cooperation(true));

        Bus::assertDispatched(GetBuildingData::class, fn (GetBuildingData $job) => $job->building->is($user->building)
            && $job->inputSource->short === InputSource::RESIDENT_SHORT
        );
        Bus::assertNotDispatched(AttachEnergyLabel::class);
        // BAG does the address and the municipality, not the build year and surface.
        Bus::assertDispatchedSync(CheckBuildingAddress::class, fn (CheckBuildingAddress $job) => $job->withBuildingFeatures === false);
    }

    public function test_registration_without_smart_twin_keeps_bag_and_ep_online(): void
    {
        $this->register($this->cooperation(false));

        Bus::assertNotDispatched(GetBuildingData::class);
        Bus::assertDispatched(AttachEnergyLabel::class);
        Bus::assertDispatchedSync(CheckBuildingAddress::class, fn (CheckBuildingAddress $job) => $job->withBuildingFeatures === true);
    }

    public function test_registration_with_smart_twin_switched_off_for_the_environment_keeps_bag_and_ep_online(): void
    {
        config()->set('hoomdossier.services.smarttwin.enabled', false);

        $this->register($this->cooperation(true));

        Bus::assertNotDispatched(GetBuildingData::class);
        Bus::assertDispatched(AttachEnergyLabel::class);
    }

    public function test_a_reset_with_smart_twin_fetches_building_data_and_leaves_bags_build_year_out(): void
    {
        $this->fakeBag();
        $user = $this->register($this->cooperation(true));
        Bus::fake([GetBuildingData::class, AttachEnergyLabel::class, CheckBuildingAddress::class]);

        app(UserService::class)->forUser($user)->resetUser(InputSource::resident());

        Bus::assertDispatched(GetBuildingData::class);
        Bus::assertNotDispatched(AttachEnergyLabel::class);
        $features = BuildingFeature::withoutGlobalScopes()
            ->where('building_id', $user->building->id)
            ->where('input_source_id', InputSource::resident()->id)
            ->first();
        $this->assertNull($features->build_year);
        $this->assertNull($features->surface);
    }

    public function test_a_reset_without_smart_twin_keeps_bag_and_ep_online(): void
    {
        $this->fakeBag();
        $user = $this->register($this->cooperation(false));
        Bus::fake([GetBuildingData::class, AttachEnergyLabel::class, CheckBuildingAddress::class]);

        app(UserService::class)->forUser($user)->resetUser(InputSource::resident());

        Bus::assertNotDispatched(GetBuildingData::class);
        Bus::assertDispatched(AttachEnergyLabel::class);
        $features = BuildingFeature::withoutGlobalScopes()
            ->where('building_id', $user->building->id)
            ->where('input_source_id', InputSource::resident()->id)
            ->first();
        $this->assertSame(1930, (int) $features->build_year);
    }

    public function test_bag_leaves_the_build_year_and_surface_to_smart_twin_for_a_building_of_a_cooperation_with_it(): void
    {
        $user = $this->register($this->cooperation(true));

        $service = Mockery::mock(BuildingAddressService::class);
        $service->shouldReceive('forBuilding', 'forInputSource', 'updateAddress', 'attachMunicipality')->andReturnSelf();
        $service->shouldNotReceive('updateBuildingFeatures');

        // As an address change or a login dispatches it: without saying, so it asks the cooperation.
        (new CheckBuildingAddress($user->building, InputSource::resident()))->handle($service);
    }

    public function test_bag_fills_in_the_build_year_and_surface_for_a_building_of_a_cooperation_without_smart_twin(): void
    {
        $user = $this->register($this->cooperation(false));

        $service = Mockery::mock(BuildingAddressService::class);
        $service->shouldReceive('forBuilding', 'forInputSource', 'updateAddress', 'attachMunicipality')->andReturnSelf();
        $service->shouldReceive('updateBuildingFeatures')->once()->andReturnSelf();

        (new CheckBuildingAddress($user->building, InputSource::resident()))->handle($service);
    }

    public function test_the_job_refreshes_the_building_data_for_its_input_source(): void
    {
        $building = Building::factory()->create();
        $coach = InputSource::coach();

        $service = Mockery::mock(BuildingDataService::class);
        $service->shouldReceive('refresh')->once()->with($building, $coach);

        (new GetBuildingData($building, $coach))->handle($service);
    }

    public function test_the_job_does_nothing_once_the_environment_is_switched_off(): void
    {
        config()->set('hoomdossier.services.smarttwin.enabled', false);

        $service = Mockery::mock(BuildingDataService::class);
        $service->shouldNotReceive('refresh');

        (new GetBuildingData(Building::factory()->create(), InputSource::coach()))->handle($service);
    }
}
