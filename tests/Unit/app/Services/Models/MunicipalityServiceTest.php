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
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

final class MunicipalityServiceTest extends TestCase
{
    use RefreshDatabase;

    public $seed = false;
    public $seeder = DatabaseSeeder::class;

    /**
     * This test checks whether the `getAvailableBagMunicipalities` method returns the correct BAG municipalities
     * saved in the mappings table. All rows that have no target mapping, or where the target is the passed
     * municipality, should be returned.
     */
    public function test_we_get_correct_available_bag_municipalities(): void
    {
        // DB seeding also seeds mappings. We clear them since we're asserting a count.
        DB::table('mappings')->delete();

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

    /**
     * This test checks of the `retrieveBagMunicipalities` method returns all the BAG municipality rows attached to the
     * passed municipality.
     */
    public function test_retrieve_bag_municipalities_returns_correct_mapped_municipalities(): void
    {
        // DB seeding also seeds mappings. We clear them since we're asserting a count.
        DB::table('mappings')->delete();

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

    /**
     * This test checks of the `retrieveVbjehuisMuncipality` method returns the mapping attached to the passed
     * municipality which links to the Vbjehuis municipality.
     */
    public function test_retrieve_vbjehuis_municipality_returns_correct_mapped_municipality(): void
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
