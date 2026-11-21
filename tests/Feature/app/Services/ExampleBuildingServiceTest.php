<?php

namespace Tests\Feature\app\Services;

use App\Models\Building;
use App\Models\ExampleBuilding;
use App\Models\ExampleBuildingContent;
use App\Models\InputSource;
use App\Models\ToolQuestion;
use App\Models\User;
use App\Services\ExampleBuildingService;
use App\Services\ToolQuestionService;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class ExampleBuildingServiceTest extends TestCase
{
    use RefreshDatabase;

    public $seed = true;
    public $seeder = DatabaseSeeder::class;

    /**
     * What the resident answered on registration (or the API passed) is theirs, not the example
     * building's: contract type and monument survive applying one, as the API's questions do.
     */
    public function test_applying_an_example_building_keeps_contract_type_and_monument(): void
    {
        $building = User::first()->building;
        $resident = InputSource::resident();
        $this->save($building, $resident, 'building-contract-type', 'rented');
        $this->save($building, $resident, 'monument', 1);

        $exampleBuilding = ExampleBuilding::factory()->create();
        ExampleBuildingContent::factory()->create([
            'example_building_id' => $exampleBuilding->id,
            'build_year' => null,
            'content' => ['surface' => 120, 'building-contract-type' => 'bought', 'monument' => 2],
        ]);

        ExampleBuildingService::apply($exampleBuilding, 1980, $building, $resident);

        $this->assertSame('rented', $building->getAnswer($resident, ToolQuestion::findByShort('building-contract-type')));
        $this->assertSame(1, (int) $building->getAnswer($resident, ToolQuestion::findByShort('monument')));
        $this->assertSame(120, (int) $building->getAnswer($resident, ToolQuestion::findByShort('surface')));
    }

    private function save(Building $building, InputSource $inputSource, string $short, mixed $answer): void
    {
        ToolQuestionService::init()
            ->toolQuestion(ToolQuestion::findByShort($short))
            ->building($building)
            ->currentInputSource($inputSource)
            ->save($answer);
    }
}
