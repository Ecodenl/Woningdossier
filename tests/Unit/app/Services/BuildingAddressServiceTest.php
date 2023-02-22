<?php

namespace Tests\Unit\app\Services;

use App\Helpers\MappingHelper;
use App\Models\Building;
use App\Models\Municipality;
use App\Models\User;
use App\Services\BuildingAddressService;
use App\Services\Lvbag\Payloads\AddressExpanded;
use App\Services\MappingService;
use Database\Seeders\DatabaseSeeder;
use Ecodenl\LvbagPhpWrapper\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Artisan;
use Mockery\MockInterface;
use Tests\TestCase;

class BuildingAddressServiceTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public $seed = true;
    public $seeder = DatabaseSeeder::class;

    protected function setUp(): void
    {
        parent::setUp();
        Artisan::call('cache:clear');
    }

    public function test_municipality_attaches_when_mapping_available()
    {
        // this woonplaats should be "Goeree-Overflakkee"
        $user = User::factory()->create();
        $building = Building::factory()->create(['user_id' => $user->id]);
        $building->update([
            'bag_woonplaats_id' => '2134',
        ]);

        $municipality = Municipality::factory()->create(['name' => 'Flakee', 'short' => 'island']);
        MappingService::init()
            ->from("Goeree-Overflakkee")
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

        $this->partialMock(
            Client::class,
            function (MockInterface $mock) {
                return $mock
                    ->shouldReceive('get')
                    ->andReturn([]);
            }
        );

        app(BuildingAddressService::class)->forBuilding($building)->updateAddress($fallbackData);

        $fallbackData['bag_addressid'] = '';
        $fallbackData['bag_woonplaats_id'] = '';

        $this->assertDatabaseHas('buildings', $fallbackData);
    }

    public function test_update_address_uses_bag_as_thruth_when_available()
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

        $mockedApiData = [
            "_embedded" => [
                "adressen" => [
                    [
                        "nummeraanduidingIdentificatie" => "1924200000030235",
                        "woonplaatsIdentificatie" => "2134",
                        "openbareRuimteNaam" => "Boezemweg",
                        "huisnummer" => $fallbackData['number'],
                        "postcode" => $fallbackData['postal_code'],
                        "woonplaatsNaam" => "Oude-Tonge",
                        "oorspronkelijkBouwjaar" => [
                            0 => "2015"
                        ],
                        "oppervlakte" => 2666,
                    ],
                ],
            ]
        ];
        $this->partialMock(
            Client::class,
            function (MockInterface $mock) use ($mockedApiData) {
                return $mock
                    ->shouldReceive('get')
                    ->andReturn($mockedApiData);
            }
        );

        app(BuildingAddressService::class)->forBuilding($building)->updateAddress($fallbackData);

        $addressExpandedData = $mockedApiData['_embedded']['adressen'][0];
        $addressExpandedData['endpoint_failure'] = false;
        $assertableBuildingData = (new AddressExpanded($addressExpandedData))->prepareForBuilding();
        $assertableBuildingData['user_id'] = $building->user_id;

        $this->assertDatabaseHas('buildings', Arr::except($assertableBuildingData, ['build_year', 'surface']));
        $this->assertDatabaseMissing('buildings', $fallbackData);
    }
}
