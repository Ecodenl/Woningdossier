<?php

namespace Tests\Feature\app\Services\SmartTwin;

use App\Enums\AdviceSource;
use App\Enums\SmartTwin\MappingStatus;
use App\Enums\SmartTwin\MappingTarget;
use App\Models\Building;
use App\Models\BuildingFeature;
use App\Models\InputSource;
use App\Models\MeasureApplication;
use App\Models\User;
use App\Models\UserActionPlanAdvice;
use App\Services\SmartTwin\Mapping\MappingApplier;
use App\Services\SmartTwin\Mapping\MappingResult;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * The applier is the only place in the mapping that writes, and it writes two kinds of thing.
 *
 * Answers go through ToolQuestionService rather than into a table, which is what gets them copied
 * to the master input source — the one the rest of the dossier reads. Advised measures go into the
 * action plan marked as ours, so a recalculation neither deletes them nor duplicates them.
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

    private function adviceFor(string $measureShort): ?UserActionPlanAdvice
    {
        return UserActionPlanAdvice::withoutGlobalScopes()
            ->where('user_id', $this->building->user_id)
            ->where('input_source_id', $this->coach->id)
            ->where('user_action_plan_advisable_type', MeasureApplication::class)
            ->where('user_action_plan_advisable_id', MeasureApplication::findByShort($measureShort)->id)
            ->first();
    }

    public function test_an_advised_measure_lands_in_the_action_plan(): void
    {
        $result = $this->apply(MappingResult::mappedAdvice('cavity-wall-insulation', 1622.65));

        $this->assertSame(MappingStatus::MAPPED, $result->status);

        $advice = $this->adviceFor('cavity-wall-insulation');

        $this->assertNotNull($advice);
        $this->assertEqualsWithDelta(1622.65, $advice->costs['to'], 0.001);
        $this->assertSame(
            MeasureApplication::findByShort('cavity-wall-insulation')->step_id,
            $advice->step_id,
        );
    }

    public function test_an_advised_measure_is_not_the_calculation_to_rebuild(): void
    {
        $this->apply(MappingResult::mappedAdvice('cavity-wall-insulation', 1622.65));

        $this->assertSame(AdviceSource::SMART_TWIN, $this->adviceFor('cavity-wall-insulation')->source);
    }

    public function test_it_leaves_no_saving_behind_next_to_a_price_it_did_not_derive(): void
    {
        $this->apply(MappingResult::mappedAdvice('cavity-wall-insulation', 1622.65));

        $advice = $this->adviceFor('cavity-wall-insulation');

        $this->assertNull($advice->savings_money);
        $this->assertNull($advice->savings_gas);
        $this->assertNull($advice->savings_electricity);
    }

    public function test_a_second_scan_writes_its_new_figures_over_the_first(): void
    {
        $this->apply(MappingResult::mappedAdvice('cavity-wall-insulation', 1622.65));
        $this->apply(MappingResult::mappedAdvice('cavity-wall-insulation', 1800.00));

        $this->assertSame(1, UserActionPlanAdvice::withoutGlobalScopes()
            ->where('user_id', $this->building->user_id)
            ->where('input_source_id', $this->coach->id)
            ->count());

        $this->assertEqualsWithDelta(1800.00, $this->adviceFor('cavity-wall-insulation')->costs['to'], 0.001);
    }

    public function test_an_unknown_measure_is_reported_as_target_missing(): void
    {
        $result = $this->apply(MappingResult::mappedAdvice('er-bestaat-geen-maatregel-met-deze-short', 500.0));

        $this->assertSame(MappingStatus::TARGET_MISSING, $result->status);
        // Reported as the advice it was meant to be, not as a tool question that went astray.
        $this->assertSame(MappingTarget::ADVICE, $result->kind);
    }
}
