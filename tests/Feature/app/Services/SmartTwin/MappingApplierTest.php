<?php

namespace Tests\Feature\app\Services\SmartTwin;

use App\Enums\AdviceSource;
use App\Enums\SmartTwin\MappingStatus;
use App\Enums\SmartTwin\MappingTarget;
use App\Models\Building;
use App\Models\BuildingFeature;
use App\Models\CustomMeasureApplication;
use App\Models\InputSource;
use App\Models\MeasureApplication;
use App\Models\User;
use App\Models\UserActionPlanAdvice;
use App\Services\SmartTwin\Mapping\Mappers\ElementValues;
use App\Services\SmartTwin\Mapping\MappingApplier;
use App\Services\SmartTwin\Mapping\MappingResult;
use App\Services\UserActionPlanAdviceService;
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

    public function test_a_cleared_answer_is_emptied_here_and_in_master(): void
    {
        // What a second scan does when the scenario no longer insulates the facade: the surface the
        // first scan wrote may not stay behind.
        $this->apply(MappingResult::mapped('insulation-wall-surface', 65.39));
        $this->apply(MappingResult::cleared('insulation-wall-surface'));

        $this->assertNull($this->featureFor($this->coach)?->insulation_wall_surface);
        $this->assertNull($this->featureFor(InputSource::findByShort(InputSource::MASTER_SHORT))?->insulation_wall_surface);
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

    public function test_a_package_card_starts_visible_in_to_do_whatever_the_dwelling_says(): void
    {
        // Left to the category logic, a facade that is already well insulated sends the card to
        // "In orde". The package is what SmartTwin calculated its saving and label for, so it starts
        // in "Nu aanpakken" as a whole.
        $this->apply(MappingResult::mapped(
            'current-wall-insulation',
            app(ElementValues::class)->idFor('wall-insulation', 5),
        ));

        $this->apply(MappingResult::mappedAdvice('cavity-wall-insulation', 1622.65));

        $advice = $this->adviceFor('cavity-wall-insulation');
        $this->assertSame(UserActionPlanAdviceService::CATEGORY_TO_DO, $advice->category);
        $this->assertTrue((bool) $advice->visible);
    }

    private function calculatedAdvice(InputSource $inputSource, string $measureShort): void
    {
        $measure = MeasureApplication::findByShort($measureShort);

        (new UserActionPlanAdvice([
            'user_id'                         => $this->building->user_id,
            'input_source_id'                 => $inputSource->id,
            'user_action_plan_advisable_type' => MeasureApplication::class,
            'user_action_plan_advisable_id'   => $measure->id,
            'step_id'                         => $measure->step_id,
            'costs'                           => ['from' => null, 'to' => 2000],
        ]))->save();
    }

    /**
     * @return array<int, string> The shorts of the measure applications advised, over every input source.
     */
    private function advisedMeasures(): array
    {
        return UserActionPlanAdvice::withoutGlobalScopes()
            ->where('user_id', $this->building->user_id)
            ->where('user_action_plan_advisable_type', MeasureApplication::class)
            ->get()
            ->map(fn (UserActionPlanAdvice $advice) => MeasureApplication::find($advice->user_action_plan_advisable_id)->short)
            ->unique()
            ->sort()
            ->values()
            ->all();
    }

    public function test_an_import_replaces_the_action_plan_for_every_input_source(): void
    {
        $resident = InputSource::findByShort(InputSource::RESIDENT_SHORT);

        // Cards the calculation made for resident and coach, a small measure, one from a previous
        // scan and an own measure.
        $this->calculatedAdvice($resident, 'floor-insulation');
        $this->calculatedAdvice($this->coach, 'floor-insulation');
        $this->calculatedAdvice($resident, 'save-energy-with-light');

        $previous = app(MappingApplier::class);
        $previous->startRun();
        $previous->apply($this->building, $resident, MappingResult::mappedAdvice('cavity-wall-insulation', 1622.65));

        $own = CustomMeasureApplication::factory()->create([
            'building_id'     => $this->building->id,
            'input_source_id' => $this->coach->id,
        ]);
        (new UserActionPlanAdvice([
            'user_id'                         => $this->building->user_id,
            'input_source_id'                 => $this->coach->id,
            'user_action_plan_advisable_type' => CustomMeasureApplication::class,
            'user_action_plan_advisable_id'   => $own->id,
            'costs'                           => ['from' => null, 'to' => 500],
        ]))->save();

        $applier = app(MappingApplier::class);
        $applier->startRun();
        $applier->apply($this->building, $this->coach, MappingResult::mappedAdvice('roof-insulation-flat-current', 1000.0));

        // The resident's calculated card and the previous scan's advice went too, not only the
        // coach's: an import overwrites the woonplan of every input source.
        $this->assertSame(['roof-insulation-flat-current', 'save-energy-with-light'], $this->advisedMeasures());
        $this->assertTrue(UserActionPlanAdvice::withoutGlobalScopes()
            ->where('user_id', $this->building->user_id)
            ->where('user_action_plan_advisable_type', CustomMeasureApplication::class)
            ->exists());
    }

    public function test_an_advice_lands_for_resident_coach_and_master(): void
    {
        $applier = app(MappingApplier::class);
        $applier->startRun();
        // Written through the coach's flow.
        $applier->apply($this->building, $this->coach, MappingResult::mappedAdvice('cavity-wall-insulation', 1622.65));

        $rows = UserActionPlanAdvice::withoutGlobalScopes()
            ->where('user_id', $this->building->user_id)
            ->where('user_action_plan_advisable_id', MeasureApplication::findByShort('cavity-wall-insulation')->id)
            ->get();

        $this->assertEqualsCanonicalizing(
            [InputSource::RESIDENT_SHORT, InputSource::COACH_SHORT, InputSource::MASTER_SHORT],
            $rows->map(fn (UserActionPlanAdvice $row) => InputSource::find($row->input_source_id)->short)->all(),
        );
        $this->assertTrue($rows->every(fn (UserActionPlanAdvice $row) => 1622.65 === (float) $row->costs['to']));
    }

    public function test_the_first_import_makes_the_own_measures_the_same_for_everyone(): void
    {
        // Added by the resident before SmartTwin: only theirs and master.
        $own = CustomMeasureApplication::factory()->create([
            'building_id'     => $this->building->id,
            'input_source_id' => InputSource::resident()->id,
        ]);
        (new UserActionPlanAdvice([
            'user_id'                         => $this->building->user_id,
            'input_source_id'                 => InputSource::resident()->id,
            'user_action_plan_advisable_type' => CustomMeasureApplication::class,
            'user_action_plan_advisable_id'   => $own->id,
            'costs'                           => ['from' => null, 'to' => 500],
        ]))->save();
        $this->assertNull($own->getSibling($this->coach));

        $applier = app(MappingApplier::class);
        $applier->startRun();
        $applier->apply($this->building, InputSource::resident(), MappingResult::mappedAdvice('cavity-wall-insulation', 1622.65));

        $coachSibling = $own->getSibling($this->coach);
        $this->assertInstanceOf(CustomMeasureApplication::class, $coachSibling);
        $this->assertTrue(UserActionPlanAdvice::withoutGlobalScopes()
            ->where('input_source_id', $this->coach->id)
            ->where('user_action_plan_advisable_type', CustomMeasureApplication::class)
            ->where('user_action_plan_advisable_id', $coachSibling->id)
            ->exists());
    }

    public function test_the_action_plan_is_cleared_once_per_import(): void
    {
        $applier = app(MappingApplier::class);
        $applier->startRun();

        $applier->apply($this->building, $this->coach, MappingResult::mappedAdvice('cavity-wall-insulation', 1622.65));
        $applier->apply($this->building, $this->coach, MappingResult::mappedAdvice('roof-insulation-flat-current', 1000.0));

        $this->assertSame(['cavity-wall-insulation', 'roof-insulation-flat-current'], $this->advisedMeasures());
    }

    public function test_answers_alone_leave_the_action_plan_alone(): void
    {
        $this->calculatedAdvice($this->coach, 'floor-insulation');

        $applier = app(MappingApplier::class);
        $applier->startRun();
        $applier->apply($this->building, $this->coach, MappingResult::mapped('wall-surface', 105.69));

        $this->assertSame(['floor-insulation'], $this->advisedMeasures());
    }

    public function test_two_solutions_for_one_measure_add_up_within_an_import(): void
    {
        $applier = app(MappingApplier::class);
        $applier->startRun();

        $applier->apply($this->building, $this->coach, MappingResult::mappedAdvice('roof-insulation-flat-current', 1000.0));
        $result = $applier->apply($this->building, $this->coach, MappingResult::mappedAdvice('roof-insulation-flat-current', 500.0));

        $this->assertEqualsWithDelta(1500.0, $this->adviceFor('roof-insulation-flat-current')->costs['to'], 0.001);
        $this->assertStringContainsString('opgeteld', $result->note);
    }

    public function test_an_unknown_measure_is_reported_as_target_missing(): void
    {
        $result = $this->apply(MappingResult::mappedAdvice('er-bestaat-geen-maatregel-met-deze-short', 500.0));

        $this->assertSame(MappingStatus::TARGET_MISSING, $result->status);
        // Reported as the advice it was meant to be, not as a tool question that went astray.
        $this->assertSame(MappingTarget::ADVICE, $result->kind);
    }
}
