<?php

namespace Tests\Feature\app\Services\SmartTwin;

use App\Enums\SmartTwin\MappingStatus;
use App\Models\Building;
use App\Models\BuildingFeature;
use App\Models\InputSource;
use App\Models\User;
use App\Services\SmartTwin\Mapping\MappingApplier;
use App\Services\SmartTwin\Mapping\MappingResult;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * The applier is the only place in the mapping that writes, and it writes through
 * ToolQuestionService rather than into a table. These tests hold on to what that buys us: the
 * answer lands where the tool question says it belongs, and it is copied to the master input
 * source — which is what the rest of the dossier reads.
 */
final class MappingApplierTest extends TestCase
{
    use RefreshDatabase;

    public $seed = true;
    public $seeder = DatabaseSeeder::class;

    private Building $building;
    private InputSource $coach;

    protected function setUp(): void
    {
        parent::setUp();

        // One user is seeded, we'll just use it.
        $this->building = User::first()->building;
        $this->coach = InputSource::findByShort(InputSource::COACH_SHORT);
    }

    private function apply(MappingResult $result): MappingResult
    {
        return app(MappingApplier::class)->apply($this->building, $this->coach, $result);
    }

    private function featureFor(InputSource $inputSource): ?BuildingFeature
    {
        return BuildingFeature::allInputSources()
            ->forBuilding($this->building)
            ->where('input_source_id', $inputSource->id)
            ->first();
    }

    public function test_it_saves_a_mapped_value_where_the_tool_question_points(): void
    {
        $result = $this->apply(MappingResult::mapped('wall-surface', 105.69));

        $this->assertSame(MappingStatus::MAPPED, $result->status);
        $this->assertEqualsWithDelta(105.69, (float) $this->featureFor($this->coach)?->wall_surface, 0.001);
    }

    public function test_it_copies_the_answer_to_the_master_input_source(): void
    {
        // The whole reason for going through ToolQuestionService: master is what the woonplan and
        // the coach read, and a direct write would leave it untouched.
        $this->apply(MappingResult::mapped('wall-surface', 105.69));

        $master = $this->featureFor(InputSource::findByShort(InputSource::MASTER_SHORT));

        $this->assertEqualsWithDelta(105.69, (float) $master?->wall_surface, 0.001);
    }

    public function test_it_overwrites_an_existing_answer(): void
    {
        $this->apply(MappingResult::mapped('wall-surface', 80.0));
        $this->apply(MappingResult::mapped('wall-surface', 105.69));

        $this->assertEqualsWithDelta(105.69, (float) $this->featureFor($this->coach)?->wall_surface, 0.001);
    }

    public function test_an_unknown_short_is_reported_as_target_missing(): void
    {
        $result = $this->apply(MappingResult::mapped('er-bestaat-geen-vraag-met-deze-short', 42));

        $this->assertSame(MappingStatus::TARGET_MISSING, $result->status);
        $this->assertSame('er-bestaat-geen-vraag-met-deze-short', $result->target);
        // The value is kept so the report shows what was lost, not just where it was headed.
        $this->assertSame(42, $result->value);
    }

    public function test_it_writes_nothing_for_a_result_that_is_not_mapped(): void
    {
        foreach ([MappingResult::skipped('metadata'), MappingResult::valueUnmapped('onbekende waarde')] as $result) {
            $this->assertSame($result, $this->apply($result));
        }

        $this->assertNull($this->featureFor($this->coach)?->wall_surface);
    }
}
