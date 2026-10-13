<?php

namespace Tests\Feature\app\Services\SmartTwin;

use App\Enums\AdviceSource;
use App\Enums\SmartTwin\EventType;
use App\Models\Building;
use App\Models\Cooperation;
use App\Models\InputSource;
use App\Models\MeasureApplication;
use App\Models\Scan;
use App\Models\Step;
use App\Models\User;
use App\Models\UserActionPlanAdvice;
use App\Services\SmartTwin\Mapping\FieldMapper;
use App\Services\SmartTwin\Mapping\Leaf;
use App\Services\SmartTwin\Mapping\MapperRegistry;
use App\Services\SmartTwin\Mapping\MappingResult;
use App\Services\SmartTwin\SmartTwinService;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * From the first SmartTwin advice on, the woonplan is SmartTwin's advice plus what the resident
 * chose. The calculated advices it replaces go, for every input source, and do not come back with a
 * recalculation while the cooperation takes part.
 */
final class AdviceTakeoverTest extends TestCase
{
    use RefreshDatabase;

    public $seed = true;
    public $seeder = DatabaseSeeder::class;

    private Cooperation $cooperation;
    private Building $building;
    private InputSource $resident;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('hoomdossier.services.smarttwin.enabled', true);

        $this->cooperation = Cooperation::factory()->create();
        $this->cooperation->scans()->attach(Scan::findByShort(Scan::QUICK)->id);

        $user = User::factory()->withAccount()->asResident()->create(['cooperation_id' => $this->cooperation->id]);
        $this->building = Building::factory()->create(['user_id' => $user->id]);
        $this->resident = InputSource::findByShort(InputSource::RESIDENT_SHORT);
    }

    private function calculatedAdvice(string $measureShort, string $stepShort): UserActionPlanAdvice
    {
        $advice = new UserActionPlanAdvice([
            'user_id'                         => $this->building->user_id,
            'input_source_id'                 => $this->resident->id,
            'user_action_plan_advisable_type' => MeasureApplication::class,
            'user_action_plan_advisable_id'   => MeasureApplication::findByShort($measureShort)->id,
            'step_id'                         => Step::findByShort($stepShort)->id,
            'costs'                           => ['from' => null, 'to' => 1000],
        ]);
        $advice->save();

        return $advice;
    }

    /**
     * Rows for this measure, over every input source (master included).
     */
    private function adviceRows(string $measureShort): \Illuminate\Support\Collection
    {
        return UserActionPlanAdvice::withoutGlobalScopes()
            ->where('user_id', $this->building->user_id)
            ->where('user_action_plan_advisable_id', MeasureApplication::findByShort($measureShort)->id)
            ->get();
    }

    /**
     * Runs the real mapping pipeline, with one mapper that advises the given measure.
     */
    private function processSmartTwinAdvice(?string $measureShort): void
    {
        AdvisingMapper::$measureShort = $measureShort;
        $this->app->instance(MapperRegistry::class, new MapperRegistry([AdvisingMapper::class]));

        $this->app->make(SmartTwinService::class)->processResults(
            $this->building,
            ['solutions' => [['id' => 'a']]],
            EventType::RESIDENT_SCAN_FINISHED,
        );
    }

    public function test_the_calculated_advices_smart_twin_replaces_go_for_every_input_source(): void
    {
        $this->calculatedAdvice('cavity-wall-insulation', 'wall-insulation');
        $this->calculatedAdvice('repair-joint', 'wall-insulation');

        $this->processSmartTwinAdvice('cavity-wall-insulation');

        // The advised measure is taken over, resident and master alike.
        $cavity = $this->adviceRows('cavity-wall-insulation');
        $this->assertCount(2, $cavity);
        $this->assertTrue($cavity->every(fn (UserActionPlanAdvice $advice) => AdviceSource::SMART_TWIN === $advice->source));

        // Not advised by SmartTwin, but calculated: gone, master included.
        $this->assertCount(0, $this->adviceRows('repair-joint'));
    }

    public function test_small_measures_stay(): void
    {
        $this->calculatedAdvice('save-energy-with-light', 'small-measures');

        $this->processSmartTwinAdvice('cavity-wall-insulation');

        $this->assertCount(2, $this->adviceRows('save-energy-with-light'));
    }

    public function test_nothing_goes_when_smart_twin_advised_nothing(): void
    {
        // An empty woonplan would invite the resident to do the check they just did.
        $this->calculatedAdvice('repair-joint', 'wall-insulation');

        $this->processSmartTwinAdvice(null);

        $this->assertCount(2, $this->adviceRows('repair-joint'));
    }

    public function test_a_recalculation_does_not_bring_a_replaced_advice_back(): void
    {
        $this->processSmartTwinAdvice('cavity-wall-insulation');

        // What a helper does when its step is recalculated.
        $this->calculatedAdvice('repair-joint', 'wall-insulation');
        $this->calculatedAdvice('save-energy-with-light', 'small-measures');

        $this->assertCount(0, $this->adviceRows('repair-joint'));
        $this->assertCount(2, $this->adviceRows('save-energy-with-light'));
    }

    public function test_the_calculation_fills_the_woonplan_again_once_the_cooperation_is_switched_off(): void
    {
        $this->processSmartTwinAdvice('cavity-wall-insulation');

        $this->cooperation->update(['smarttwin_enabled' => false]);
        $this->calculatedAdvice('repair-joint', 'wall-insulation');

        $this->assertCount(2, $this->adviceRows('repair-joint'));
    }
}

/**
 * Advises one measure for every solution in the response, or nothing.
 */
final class AdvisingMapper implements FieldMapper
{
    public static ?string $measureShort = null;

    public function paths(): array
    {
        return ['solutions.*.id'];
    }

    public function map(Building $building, Leaf $leaf, array $response): MappingResult
    {
        return is_null(static::$measureShort)
            ? MappingResult::skipped('nothing advised')
            : MappingResult::mappedAdvice(static::$measureShort, 1500.0);
    }
}
