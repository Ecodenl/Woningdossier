<?php

namespace Tests\Unit\app\Services\Models;

use App\Helpers\MappingHelper;
use App\Models\Building;
use App\Models\CustomMeasureApplication;
use App\Models\InputSource;
use App\Models\Mapping;
use App\Models\Municipality;
use App\Services\MappingService;
use App\Services\Models\BuildingService;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class BuildingServiceTest extends TestCase
{
//    use RefreshDatabase;

//    public $seed = true;
//    public $seeder = DatabaseSeeder::class;

    protected function setUp(): void
    {
        parent::setUp();
        Artisan::call('cache:clear');
    }

    public function test_municipality_attaches_when_mapping_available()
    {
        // this woonplaats should be "Goeree-Overflakkee"
        $building = Building::factory()->create([
            'bag_woonplaats_id' => '2134',
        ]);

        $municipality = Municipality::factory()->create(['name' => 'Flakee', 'short' => 'island']);
        MappingService::init()
            ->from("Goeree-Overflakkee")
            ->sync([$municipality], MappingHelper::TYPE_MUNICIPALITY);

        BuildingService::init($building)->attachMunicipality();

        $this->assertDatabaseHas('buildings', ['id' => $building->id, 'municipality_id' => $municipality->id]);
    }
}
