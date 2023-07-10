<?php

namespace Tests\Unit\app\Services;

use App\Events\BuildingAddressUpdated;
use App\Helpers\MappingHelper;
use App\Models\Building;
use App\Models\BuildingFeature;
use App\Models\InputSource;
use App\Models\Municipality;
use App\Models\User;
use App\Services\BuildingAddressService;
use App\Services\Lvbag\BagService;
use App\Services\Lvbag\Payloads\AddressExpanded;
use App\Services\MappingService;
use Database\Seeders\DatabaseSeeder;
use Ecodenl\LvbagPhpWrapper\Lvbag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Tests\MocksLvbag;
use Tests\TestCase;
use Illuminate\Support\Facades\Event;
use Mockery;

class BuildingAddressServiceTest extends TestCase
{
    use WithFaker,
        MocksLvbag,
        RefreshDatabase;

    public $seed = true;
    public $seeder = DatabaseSeeder::class;

    public function test_municipality_attaches_when_mapping_available()
    {
        // We don't want to send emails, etc.
        $this->withoutEvents();

        $user = User::factory()->create();
        $building = Building::factory()->create(['user_id' => $user->id]);
        $building->update([
            // the id doesnt really matter in this case as the endpoint will always return a valid value due to mock.
            'bag_woonplaats_id' => '1234',
        ]);

        $municipality = Municipality::factory()->create();

        $fromMunicipalityName = $this->faker->randomElement([
            'Hatsikidee-Flakkee',
            'Hellevoetsluis',
            'Haarlem',
            'Hollywood'
        ]);
        $this->mockLvbagClientWoonplaats($fromMunicipalityName)->createLvbagMock();
        MappingService::init()
            ->from($fromMunicipalityName)
            ->sync([$municipality], MappingHelper::TYPE_BAG_MUNICIPALITY);

        app(BuildingAddressService::class)->forBuilding($building)->attachMunicipality();

        $this->assertDatabaseHas('buildings', ['id' => $building->id, 'municipality_id' => $municipality->id]);
    }

    public function test_update_address_uses_fallback_on_empty_bag_response()
    {
        $fallbackData = [
            'street' => $this->faker->streetName,
            'number' => $this->faker->numberBetween(3, 22),
            'city' => 'bubba',
            'extension' => 'd',
            'postal_code' => $this->faker->postcode,
        ];

        $user = User::factory()->create();
        $building = Building::factory()->create([
            'bag_addressid' => 32443234234,
            'bag_woonplaats_id' => 2433,
            'user_id' => $user->id
        ]);

        $this->createLvbagMock();

        app(BuildingAddressService::class)->forBuilding($building)->updateAddress($fallbackData);

        $fallbackData['bag_addressid'] = '';
        $fallbackData['bag_woonplaats_id'] = '';

        $this->assertDatabaseHas('buildings', $fallbackData);
    }

    public function test_building_address_updated_dispatches_after_municipality_changed()
    {
        // this woonplaats should be "Goeree-Overflakkee"
        $user = User::factory()->create();
        $building = Building::factory()->create(['user_id' => $user->id]);
        $building->update([
            // the id doesnt really matter in this case as the endpoint will always return a valid value due to mock.
            'bag_woonplaats_id' => '2134',
        ]);

        $municipality = Municipality::factory()->create();

        $fromMunicipalityName = $this->faker->randomElement([
            'Hatsikidee-Flakkee',
            'Hellevoetsluis',
            'Haarlem',
            'Hollywood'
        ]);
        $this->mockLvbagClientWoonplaats($fromMunicipalityName)->createLvbagMock();
        MappingService::init()
            ->from($fromMunicipalityName)
            ->sync([$municipality], MappingHelper::TYPE_BAG_MUNICIPALITY);

        Event::fake();
        app(BuildingAddressService::class)->forBuilding($building)->attachMunicipality();

        Event::assertDispatched(BuildingAddressUpdated::class);
    }

    public function test_building_address_updated_does_not_dispatch_after_no_change_in_municipality()
    {
        // this woonplaats should be "Goeree-Overflakkee"
        $user = User::factory()->create();
        $building = Building::factory()->create(['user_id' => $user->id]);
        // while this test is ok, it does not fake client response..
        $municipality = Municipality::factory()->create();

        $fromMunicipalityName = $this->faker->randomElement([
            'Hatsikidee-Flakkee',
            'Hellevoetsluis',
            'Haarlem',
            'Hollywood'
        ]);
        $this->mockLvbagClientWoonplaats($fromMunicipalityName)->createLvbagMock();

        $building->update([
            'bag_woonplaats_id' => '2134',
            'municipality_id' => $municipality->id,
        ]);

        MappingService::init()
            ->from($fromMunicipalityName)
            ->sync([$municipality], MappingHelper::TYPE_BAG_MUNICIPALITY);

        Event::fake();

        app(BuildingAddressService::class)->forBuilding($building)->attachMunicipality();
        Event::assertNotDispatched(BuildingAddressUpdated::class);
    }

    public function test_update_address_uses_bag_as_truth_when_available()
    {
        $fallbackData = [
            'street' => $this->faker->streetName,
            'number' => $this->faker->numberBetween(3, 22),
            'city' => 'bubba',
            'extension' => 'd',
            'postal_code' => $this->faker->postcode,
        ];

        $user = User::factory()->create();
        $building = Building::factory()->create([
            'bag_addressid' => 32443234234,
            'bag_woonplaats_id' => 2433,
            'user_id' => $user->id
        ]);

        $this->mockLvbagClientAdresUitgebreid($fallbackData)->createLvbagMock();
        $mockedApiData = $this->getMockedApiData();

        app(BuildingAddressService::class)->forBuilding($building)->updateAddress($fallbackData);

        $addressExpandedData = $mockedApiData['_embedded']['adressen'][0];
        $addressExpandedData['endpoint_failure'] = false;
        $assertableBuildingData = (new AddressExpanded($addressExpandedData))->prepareForBuilding();
        $assertableBuildingData['user_id'] = $building->user_id;

        $this->assertDatabaseHas('buildings', Arr::except($assertableBuildingData, ['build_year', 'surface']));
        $this->assertDatabaseMissing('buildings', $fallbackData);
    }

    public function test_empty_bag_woonplaats_id_doesnt_call_bag()
    {
        $user = User::factory()->create();
        $building = Building::factory()->create([
            'user_id' => $user->id,
            'bag_woonplaats_id' => null, // Force null!
        ]);

        $this->createLvbagMock();

        $spy = $this->spy(BagService::class);

        app(BuildingAddressService::class)->forBuilding($building)->attachMunicipality();

        // Assert the method was not called.
        $spy->shouldNotHaveReceived('showCity');
    }

    public function test_wrong_bag_woonplaats_id_throws_error()
    {
        $user = User::factory()->create();
        $building = Building::factory()->create([
            'user_id' => $user->id,
            'bag_woonplaats_id' => 100, // BAG is ALWAYS 4 digits, so this is wrong.
        ]);

        $this->createLvbagMock();

        // Build spy with constructor args, else the constructor is skipped.
        $spy = Mockery::spy(BagService::class, [app(Lvbag::class)]);
        $this->instance(BagService::class, $spy);

        app(BuildingAddressService::class)->forBuilding($building)->attachMunicipality();

        // Assert the method was called with given parameters. We cannot check if the return value is anything useful,
        // since we cannot test external services.
        $spy->shouldHaveReceived('showCity')
            ->with(100, ['expand' => 'true'])
            ->once();
    }

    public function test_update_building_features_does_not_overwrite_present_data_with_bag()
    {
        $fallbackData = [
            'street' => $this->faker->streetName,
            'number' => $this->faker->numberBetween(3, 22),
            'city' => 'bubba',
            'extension' => 'd',
            'postal_code' => $this->faker->postcode,
        ];

        $user = User::factory()->create();
        $building = Building::factory()->create([
            'bag_addressid' => 32443234234,
            'bag_woonplaats_id' => 2433,
            'user_id' => $user->id
        ]);

        $this->mockLvbagClientAdresUitgebreid($fallbackData)->createLvbagMock();
        $mockedApiData = $this->getMockedApiData();

        $addressExpandedData = $mockedApiData['_embedded']['adressen'][0];
        $addressExpandedData['endpoint_failure'] = false;
        $assertableBuildingData = (new AddressExpanded($addressExpandedData))->prepareForBuilding();

        BuildingFeature::factory()->create([
            'building_id' => $building->id,
            'input_source_id' => InputSource::resident()->id,
            'build_year' => 1300,
            'surface' => 120,
        ]);

        app(BuildingAddressService::class)->forBuilding($building)->updateBuildingFeatures($fallbackData);

        $this->assertDatabaseHas('building_features', [
            'building_id' => $building->id,
            'input_source_id' => InputSource::resident()->id,
            'build_year' => 1300,
            'surface' => 120,
        ]);
        $this->assertDatabaseMissing('building_features', [
            'building_id' => $building->id,
            'input_source_id' => InputSource::resident()->id,
            'build_year' => $assertableBuildingData['build_year'],
            'surface' => $assertableBuildingData['surface'],
        ]);
    }

    public function test_update_building_features_sets_data_from_bag_when_data_empty()
    {
        $fallbackData = [
            'street' => $this->faker->streetName,
            'number' => $this->faker->numberBetween(3, 22),
            'city' => 'bubba',
            'extension' => 'd',
            'postal_code' => $this->faker->postcode,
        ];

        $user = User::factory()->create();
        $building = Building::factory()->create([
            'bag_addressid' => 32443234234,
            'bag_woonplaats_id' => 2433,
            'user_id' => $user->id
        ]);

        $this->mockLvbagClientAdresUitgebreid($fallbackData)->createLvbagMock();
        $mockedApiData = $this->getMockedApiData();

        $addressExpandedData = $mockedApiData['_embedded']['adressen'][0];
        $addressExpandedData['endpoint_failure'] = false;
        $assertableBuildingData = (new AddressExpanded($addressExpandedData))->prepareForBuilding();

        BuildingFeature::factory()->create([
            'building_id' => $building->id,
            'input_source_id' => InputSource::resident()->id,
        ]);

        app(BuildingAddressService::class)->forBuilding($building)->updateBuildingFeatures($fallbackData);

        $this->assertDatabaseHas('building_features', [
            'building_id' => $building->id,
            'input_source_id' => InputSource::resident()->id,
            'build_year' => $assertableBuildingData['build_year'],
            'surface' => $assertableBuildingData['surface'],
        ]);

        $this->assertDatabaseMissing('building_features', [
            'building_id' => $building->id,
            'input_source_id' => InputSource::resident()->id,
            'build_year' => null,
            'surface' => null,
        ]);
    }
}
