<?php

namespace Tests\Feature\app\Services;

use App\Models\Building;
use App\Models\BuildingRoofType;
use App\Models\InputSource;
use App\Models\RoofType;
use App\Models\ToolQuestion;
use App\Models\User;
use App\Services\ToolQuestionService;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * How roof answers land in building_roof_types: one row per roof shape, each holding its surfaces.
 */
final class RoofAnswersTest extends TestCase
{
    use RefreshDatabase;

    public $seed = true;
    public $seeder = DatabaseSeeder::class;

    private Building $building;
    private InputSource $coach;

    protected function setUp(): void
    {
        parent::setUp();

        $this->building = User::first()->building;
        $this->coach = InputSource::findByShort(InputSource::COACH_SHORT);
    }

    private function save(string $short, mixed $answer): void
    {
        ToolQuestionService::init()
            ->toolQuestion(ToolQuestion::findByShort($short))
            ->building($this->building)
            ->currentInputSource($this->coach)
            ->save($answer);
    }

    private function row(string $roofTypeShort, InputSource $inputSource): ?BuildingRoofType
    {
        return BuildingRoofType::allInputSources()
            ->where('building_id', $this->building->id)
            ->where('input_source_id', $inputSource->id)
            ->where('roof_type_id', RoofType::findByShort($roofTypeShort)->id)
            ->first();
    }

    public function test_a_roof_surface_keeps_its_decimals(): void
    {
        // As an integer column, MySQL stored this as 40.
        $this->save('pitched-roof-surface', 39.75);
        $this->save('pitched-roof-insulation-surface', 30.73);

        $row = $this->row('pitched', $this->coach);

        $this->assertEqualsWithDelta(39.75, (float) $row->roof_surface, 0.001);
        $this->assertEqualsWithDelta(30.73, (float) $row->insulation_roof_surface, 0.001);
        $this->assertEqualsWithDelta(
            39.75,
            (float) $this->row('pitched', InputSource::findByShort(InputSource::MASTER_SHORT))->roof_surface,
            0.001,
        );
    }

    public function test_the_shape_questions_make_one_row_per_shape(): void
    {
        // The SmartTwin roof mapping relies on this: it writes the questions per shape and lets
        // current-roof-types read the rows back, rather than saving current-roof-types itself.
        $this->save('pitched-roof-surface', 39.75);
        $this->save('flat-roof-surface', 30.73);

        $this->assertNotNull($this->row('pitched', $this->coach));
        $this->assertNotNull($this->row('flat', $this->coach));

        $answer = $this->building->getAnswer($this->coach, ToolQuestion::findByShort('current-roof-types'));
        $this->assertEqualsCanonicalizing(
            [RoofType::findByShort('pitched')->id, RoofType::findByShort('flat')->id],
            $answer,
        );
    }

    public function test_saving_current_roof_types_with_two_shapes_keeps_only_one(): void
    {
        // Recorded, not endorsed. The special case in ToolQuestionService::saveToolQuestionValuables()
        // runs updateOrCreate per value on a where without roof_type_id, so the second value
        // overwrites the roof type of the row the first one made. Which is why the SmartTwin roof
        // mapping never saves this question; see the test above.
        $this->save('current-roof-types', [
            RoofType::findByShort('pitched')->id,
            RoofType::findByShort('flat')->id,
        ]);

        $this->assertNull($this->row('pitched', $this->coach));
        $this->assertNotNull($this->row('flat', $this->coach));
    }
}
