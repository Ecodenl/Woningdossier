<?php

namespace Tests\Feature\app\Jobs\SmartTwin;

use App\Enums\AdviceSource;
use App\Enums\SmartTwin\EventType;
use App\Jobs\ProcessRecalculate;
use App\Jobs\SmartTwin\UndoSmartTwinForBuilding;
use App\Models\Building;
use App\Models\CompletedSubStep;
use App\Models\Cooperation;
use App\Models\InputSource;
use App\Models\MeasureApplication;
use App\Models\Scan;
use App\Models\SmartTwinPackage;
use App\Models\Step;
use App\Models\SubStep;
use App\Models\User;
use App\Models\UserActionPlanAdvice;
use App\Services\Models\SubStepService;
use App\Services\SmartTwin\WoonplanLock;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

/**
 * Switching a cooperation off takes each of its buildings back to Hoomdossier without SmartTwin:
 * no open callbacks, no SmartTwin advices, the questions SmartTwin left open asked again, and the
 * woonplan recalculated.
 */
final class UndoSmartTwinForBuildingTest extends TestCase
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

        // The recalculation is requested, not run: what it calculates is not what is under test.
        Bus::fake([ProcessRecalculate::class]);

        $this->cooperation = Cooperation::factory()->create();
        $this->cooperation->scans()->attach(Scan::findByShort(Scan::QUICK)->id);

        $user = User::factory()->withAccount()->asResident()->create(['cooperation_id' => $this->cooperation->id]);
        $this->building = Building::factory()->create(['user_id' => $user->id]);
        $this->resident = InputSource::findByShort(InputSource::RESIDENT_SHORT);
    }

    private function advice(string $measureShort, string $stepShort, ?AdviceSource $source): void
    {
        (new UserActionPlanAdvice([
            'user_id'                         => $this->building->user_id,
            'input_source_id'                 => $this->resident->id,
            'user_action_plan_advisable_type' => MeasureApplication::class,
            'user_action_plan_advisable_id'   => MeasureApplication::findByShort($measureShort)->id,
            'step_id'                         => Step::findByShort($stepShort)->id,
            'costs'                           => ['from' => null, 'to' => 1000],
            'source'                          => $source,
        ]))->save();
    }

    private function subStep(string $stepShort, string $name): SubStep
    {
        return SubStep::where('step_id', Step::findByShort($stepShort)->id)
            ->get()
            ->firstOrFail(fn (SubStep $subStep) => $subStep->getTranslation('name', 'nl') === $name);
    }

    private function isCompleted(SubStep $subStep): bool
    {
        return CompletedSubStep::allInputSources()
            ->forInputSource(InputSource::master())
            ->forBuilding($this->building)
            ->where('sub_step_id', $subStep->id)
            ->exists();
    }

    private function switchOffAndUndo(): void
    {
        $this->cooperation->update(['smarttwin_enabled' => false]);

        UndoSmartTwinForBuilding::dispatchSync($this->building->fresh());
    }

    public function test_open_callbacks_are_dropped(): void
    {
        $this->building->setSmartTwinCallback(EventType::RESIDENT_SCAN_FINISHED, ['DossierId' => 'd-1']);
        $this->building->saveQuietly();

        $this->switchOffAndUndo();

        $this->assertSame([], $this->building->fresh()->getSmartTwinCallbacks());
    }

    public function test_the_smart_twin_advices_go_and_the_rest_stays(): void
    {
        $this->advice('save-energy-with-light', 'small-measures', null);
        $this->advice('cavity-wall-insulation', 'wall-insulation', AdviceSource::SMART_TWIN);

        $this->switchOffAndUndo();

        $rows = UserActionPlanAdvice::withoutGlobalScopes()->where('user_id', $this->building->user_id)->get();

        // Resident and master of the small measure; nothing of SmartTwin on any input source.
        $this->assertCount(2, $rows);
        $this->assertTrue($rows->every(fn (UserActionPlanAdvice $advice) => is_null($advice->source)));
    }

    public function test_the_package_and_the_lock_go_with_the_advices(): void
    {
        SmartTwinPackage::create([
            'building_id'             => $this->building->id,
            'measure_application_ids' => [MeasureApplication::findByShort('cavity-wall-insulation')->id],
            'imported_at'             => now(),
        ]);
        app(WoonplanLock::class)->lock($this->building);

        $this->switchOffAndUndo();

        $this->assertNull($this->building->smartTwinPackage()->first());
        $this->assertFalse(app(WoonplanLock::class)->isLocked($this->building));
    }

    public function test_a_completed_sub_step_missing_a_required_answer_is_asked_again(): void
    {
        // Completed while SmartTwin made nothing required, with the question left empty.
        $subStep = $this->subStep('living-requirements', 'Hoelang blijven wonen');
        SubStepService::init()->building($this->building)->inputSource($this->resident)->subStep($subStep)->complete();
        $this->assertTrue($this->isCompleted($subStep));

        $this->switchOffAndUndo();

        $this->assertFalse($this->isCompleted($subStep));
    }

    public function test_what_smart_twin_answered_is_not_asked_again(): void
    {
        // A sub step of a step SmartTwin provided, never asked, with no required question open.
        $answered = $this->subStep('building-data', 'Monument en energielabel');
        // Same step, its required question left open by SmartTwin.
        $open = $this->subStep('building-data', 'Woning type');

        $this->switchOffAndUndo();

        $this->assertTrue($this->isCompleted($answered));
        $this->assertFalse($this->isCompleted($open));
    }

    public function test_the_woonplan_is_recalculated_for_the_resident_and_the_coach(): void
    {
        $this->switchOffAndUndo();

        // One recalculation chain per input source.
        Bus::assertDispatchedTimes(ProcessRecalculate::class, 2);
    }
}
