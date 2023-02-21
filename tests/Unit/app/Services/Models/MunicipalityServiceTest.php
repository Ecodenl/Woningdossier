<?php

namespace Tests\Unit\app\Services\Models;

use App\Helpers\Arr;
use App\Helpers\MappingHelper;
use App\Models\Mapping;
use App\Models\Municipality;
use App\Services\MappingService;
use App\Services\Models\MunicipalityService;
use App\Services\Verbeterjehuis\RegulationService;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MunicipalityServiceTest extends TestCase
{
    use RefreshDatabase;

    public $seed = false;
    public $seeder = DatabaseSeeder::class;

    public function test_we_get_correct_bag_municipalities()
    {
        $municipality1 = Municipality::factory()->create([
            'name' => 'Voorne aan Zee',
            'short' => 'voorne-aan-zee',
        ]);
        $municipality2 = Municipality::factory()->create([
            'name' => 'Rotterdam',
            'short' => 'rotterdam',
        ]);

        $mappingService = MappingService::init();

        foreach (['Hellevoetsluis', 'Voorne aan Zee', 'Westvoorne', 'Rotterdam', 'Goeree-Overflakkee'] as $bagMunicipality) {
            $mappingService->from($bagMunicipality)
                ->sync([], MappingHelper::TYPE_BAG_MUNICIPALITY);
        }

        $this->assertDatabaseCount('mappings', 5);

        $municipalityService = MunicipalityService::init()->forMunicipality($municipality1);

        // Nothing linked yet so all 5 should be available.
        $availableBags = $municipalityService->getAvailableBagMunicipalities();
        $this->assertCount(5, $availableBags);

        // Sync to first municipality
        foreach (['Hellevoetsluis', 'Voorne aan Zee', 'Westvoorne'] as $bagMunicipality) {
            $mappingService->from($bagMunicipality)
                ->sync([$municipality1], MappingHelper::TYPE_BAG_MUNICIPALITY);
        }

        // Linked to this one, but nothing else yet so all 5 should be available.
        $availableBags = $municipalityService->getAvailableBagMunicipalities();
        $this->assertCount(5, $availableBags);

        // In comparison, for the other municipality, only 2 should be.
        $availableBags = $municipalityService->forMunicipality($municipality2)->getAvailableBagMunicipalities();
        $this->assertCount(2, $availableBags);
    }

    public function test_we_get_correct_vbjehuis_municipalities()
    {
        $vbjehuisMunicipalities = RegulationService::init()->getFilters()['Cities'];

        if (empty($vbjehuisMunicipalities)) {
            $this->fail('VerbeterJeHuis not available, test could not be resolved.');
        }

        $municipality1 = Municipality::factory()->create([
            'name' => 'Voorne aan Zee',
            'short' => 'voorne-aan-zee',
        ]);
        $municipality2 = Municipality::factory()->create([
            'name' => 'Rotterdam',
            'short' => 'rotterdam',
        ]);

        $municipalityService = MunicipalityService::init()->forMunicipality($municipality1);
        $vbjehuis = Arr::first($vbjehuisMunicipalities);
        MappingService::init()->from($municipality1)->sync([$vbjehuis], MappingHelper::TYPE_MUNICIPALITY_VBJEHUIS);

        // Get municipalities should be all
        $availableVbjehuis = $municipalityService->getAvailableVbjehuisMunicipalities();
        $this->assertCount(count($vbjehuisMunicipalities), $availableVbjehuis);

        // In comparison, for the other municipality, all - 1 should be.
        $availableVbjehuis = $municipalityService->forMunicipality($municipality2)->getAvailableVbjehuisMunicipalities();
        $this->assertCount(count($vbjehuisMunicipalities) - 1, $availableVbjehuis);
    }

    public function test_retrieve_bag_municipalities()
    {
        $municipality = Municipality::factory()->create([
            'name' => 'Voorne aan Zee',
            'short' => 'voorne-aan-zee',
        ]);

        $mappingService = MappingService::init();

        foreach (['Hellevoetsluis', 'Voorne aan Zee', 'Westvoorne', 'Rotterdam', 'Goeree-Overflakkee'] as $bagMunicipality) {
            $mappingService->from($bagMunicipality)
                ->sync([], MappingHelper::TYPE_BAG_MUNICIPALITY);
        }

        foreach (['Hellevoetsluis', 'Voorne aan Zee', 'Westvoorne'] as $bagMunicipality) {
            $mappingService->from($bagMunicipality)
                ->sync([$municipality], MappingHelper::TYPE_BAG_MUNICIPALITY);
        }

        $municipalityService = MunicipalityService::init()->forMunicipality($municipality);
        $linkedBagMunicipalities = $municipalityService->retrieveBagMunicipalities();
        $this->assertDatabaseCount('mappings', 5);
        $this->assertCount(3, $linkedBagMunicipalities);
    }

    public function test_retrieve_vbjehuis_municipalities()
    {
        $municipality = Municipality::factory()->create([
            'name' => 'Voorne aan Zee',
            'short' => 'voorne-aan-zee',
        ]);

        // Link 1 to municipality, one not.
        $mappingService = MappingService::init();
        $mappingService->from($municipality)
            ->sync([['Name' => 'Voorne aan Zee', 'Id' => '3148']], MappingHelper::TYPE_MUNICIPALITY_VBJEHUIS);

        $municipalityService = MunicipalityService::init()->forMunicipality($municipality);
        $vbjeHuisMunicipality = $municipalityService->retrieveVbjehuisMuncipality();
        $this->assertInstanceOf(Mapping::class, $vbjeHuisMunicipality);
    }
}
