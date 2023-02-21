<?php

namespace Tests\Unit\app\Services;

use App\Helpers\MappingHelper;
use App\Models\Building;
use App\Models\CustomMeasureApplication;
use App\Models\InputSource;
use App\Models\Mapping;
use App\Models\Municipality;
use App\Services\MappingService;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class MappingServiceTest extends TestCase
{
    use RefreshDatabase;

    public $seed = false;
    public $seeder = DatabaseSeeder::class;

    public function test_sync_maps_correct_from_value_to_targetless()
    {
        MappingService::init()
            ->from("Hellevoetsluis")
            ->sync([], MappingHelper::TYPE_BAG_MUNICIPALITY);

        $this->assertDatabaseHas('mappings', [
            'from_value' => 'Hellevoetsluis',
            'type' => MappingHelper::TYPE_BAG_MUNICIPALITY
        ]);
    }

    public function test_sync_maps_correct_from_value_to_morph()
    {
        $target1 = Municipality::factory()->create();

        $mappingService = MappingService::init();

        $mappingService
            ->from('Oude-Tonge')
            ->sync([$target1]);

        $this->assertDatabaseHas('mappings', [
            'from_value' => 'Oude-Tonge',
            'target_model_type' => $target1->getMorphClass(),
            'target_model_id' => $target1->id,
        ]);
    }

    public function test_sync_maps_correct_from_morph_to_target_data()
    {
        $from = CustomMeasureApplication::factory()->create([
            'building_id' => Building::factory()->create(),
            'input_source_id' => InputSource::factory()->create(),
        ]);
        $target = ["Label" => "Muur", "Value" => "2933", "Highlight" => false];

        $mappingService = MappingService::init();

        $mappingService
            ->from($from)
            ->sync([$target]);

        $this->assertDatabaseHas('mappings', [
            'from_model_type' => $from->getMorphClass(),
            'from_model_id' => $from->id,
            'target_data' => $this->castAsJson($target),
        ]);
    }

    public function test_resolve_target_returns_target_morph()
    {
        $target = Municipality::factory()->create();
        Mapping::factory()->create([
            'target_model_type' => $target->getMorphClass(),
            'target_model_id' => $target->id,
            'from_value' => 'Oude-Tonge'
        ]);

        $resolvedTarget = MappingService::init()
            ->from('Oude-Tonge')
            ->resolveTarget()
            ->first();

        $this->assertEquals($target->attributesToArray(), $resolvedTarget->attributesToArray());
    }

    public function test_resolve_target_returns_target_data()
    {
        $from = Municipality::factory()->create();
        $target = ["Label" => "Muur", "Value" => "2933", "Highlight" => false];
        Mapping::factory()->create([
            'from_model_type' => $from->getMorphClass(),
            'from_model_id' => $from->id,
            'target_data' => $target,
        ]);

        $resolvedTarget = MappingService::init()
            ->from($from)
            ->resolveTarget()
            ->first();

        $this->assertEquals($target, $resolvedTarget);
    }

    public function test_resolve_target_returns_nothing_when_from_is_empty()
    {
        $from = Municipality::factory()->create();
        $target = ["Label" => "Muur", "Value" => "2933", "Highlight" => false];
        Mapping::factory()->create([
            'from_model_type' => $from->getMorphClass(),
            'from_model_id' => $from->id,
            'target_data' => $target,
        ]);

        // first we will want to assert that the database is not empty, else the test is useless.
        // its not necessary but gives more confidence in the test
        $this->assertDatabaseCount('mappings', 1);

        $resolvedTarget = MappingService::init()
            ->from(null)
            ->resolveTarget()
            ->first();

        $this->assertNull($resolvedTarget);
    }

    public function test_type_resolving()
    {
        $municipality = Municipality::factory()->create([
            'name' => 'Voorne aan Zee',
            'short' => 'voorne-aan-zee',
        ]);

        $service = MappingService::init();

        foreach (['Hellevoetsluis', 'Voorne aan Zee', 'Westvoorne'] as $bagMunicipality) {
            $service->from($bagMunicipality)
                ->sync([$municipality], MappingHelper::TYPE_BAG_MUNICIPALITY);
        }

        $service->from($municipality)
            ->sync([['Name' => 'Voorne aan Zee', 'Id' => '3148']], MappingHelper::TYPE_MUNICIPALITY_VBJEHUIS);

        $service->from('Irrelevant')->sync([['Data' => 'Something']]);

        // We should now have 5 entries
        $this->assertDatabaseCount('mappings', 5);

        // We should have 1 vbjehuis
        $vbjeHuis = $service->from($municipality)
            ->type(MappingHelper::TYPE_MUNICIPALITY_VBJEHUIS)
            ->resolveTarget();

        $this->assertCount(1, $vbjeHuis);

        // We should have 3 BAG. NOTE WE ARE GOING THE OTHER WAY HERE!
        $bag = $service->target($municipality)
            ->type(MappingHelper::TYPE_BAG_MUNICIPALITY)
            ->retrieveResolvable();

        $this->assertCount(3, $bag);

        // Add another bag mapping without target
        $service->from("Rotterdam")
            ->sync([], MappingHelper::TYPE_BAG_MUNICIPALITY);

        $this->assertDatabaseCount('mappings', 6);

        // We reset the target! We want to get ALL of type, disregarding the mappings.
        $allBags = $service->target(null)->type(MappingHelper::TYPE_BAG_MUNICIPALITY)->retrieveResolvable();

        // 3 with from, 1 added, should be 4
        $this->assertCount(4, $allBags);
    }
}
