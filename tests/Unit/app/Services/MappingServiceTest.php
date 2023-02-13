<?php

namespace Tests\Unit\app\Services;

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

    public $seed = true;
    public $seeder = DatabaseSeeder::class;

    protected function setUp(): void
    {
        parent::setUp();
        Artisan::call('cache:clear');
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
            ->resolveTarget();

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
            ->resolveTarget();

        $this->assertEquals($target, $resolvedTarget);
    }
}
