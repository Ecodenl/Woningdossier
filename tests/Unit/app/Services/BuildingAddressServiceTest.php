<?php

namespace Tests\Unit\app\Services;

use App\Helpers\MappingHelper;
use App\Models\Building;
use App\Models\Municipality;
use App\Models\User;
use App\Services\BuildingAddressService;
use App\Services\Lvbag\BagService;
use App\Services\MappingService;
use App\Services\Models\BuildingService;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
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
        $building = User::factory()->create()->building;
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

    public function test_update_address_uses_fallback_when_bag_down()
    {
        $fallbackData = [
            'street' => $this->faker->streetName,
            'number' => $this->faker->numberBetween(3, 22),
            'city' => 'bubba',
            'extension' => 'd',
            'postal_code' => $this->faker->postcode,
        ];

        $building = User::factory()->create()->building;

        $this->mock(
            BagService::class,
            function (MockInterface $mock) use ($fallbackData) {
                return $mock->shouldReceive('firstAddress')
                    ->once()
                    ->andReturn([
                        'bag_addressid' => '',
                        'bag_woonplaats_id' => '',
                        'street' => '',
                        'number' => $fallbackData['number'],
                        'postal_code' => $fallbackData['postal_code'],
                        'city' => '',
                        'build_year' => 1930,
                        'surface' => 0,
                    ]);
            }
        );

        app(BuildingAddressService::class)->forBuilding($building)->updateAddress($fallbackData);

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


        $building = User::factory()->create()->building;

        $mockedApiData = [
            'bag_addressid' => '237984',
            'bag_woonplaats_id' => '2783',
            'street' => 'Boezemweg',
            'number' => $fallbackData['number'],
            'postal_code' => $fallbackData['postal_code'],
            'city' => 'Oude-Tonge',
            'build_year' => 1930,
            'surface' => 120,
        ];
        $this->mock(
            BagService::class,
            function (MockInterface $mock) use ($mockedApiData) {
                return $mock->shouldReceive('firstAddress')
                    ->once()
                    ->andReturn($mockedApiData);
            }
        );

        app(BuildingAddressService::class)->forBuilding($building)->updateAddress($fallbackData);

        $mockedApiData['user_id'] = $building->user_id;

        $this->assertDatabaseHas('buildings', Arr::except($mockedApiData, ['build_year', 'surface']));
        $this->assertDatabaseMissing('buildings', $fallbackData);
    }
}
