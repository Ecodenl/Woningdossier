<?php

namespace Tests\Feature\app\Livewire\Cooperation\Frontend\Tool\SimpleScan;

use App\Enums\AdviceSource;
use App\Helpers\HoomdossierSession;
use App\Helpers\RoleHelper;
use App\Livewire\Cooperation\Frontend\Tool\SimpleScan\Form;
use App\Models\Building;
use App\Models\Cooperation;
use App\Models\InputSource;
use App\Models\MeasureApplication;
use App\Models\Role;
use App\Models\Scan;
use App\Models\Step;
use App\Models\SubStep;
use App\Models\User;
use App\Models\UserActionPlanAdvice;
use App\Services\Models\BuildingService;
use App\Services\WoonplanService;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * HD-20: a new resident of a cooperation with SmartTwin cannot get the quick scan calculable before
 * SmartTwin's first advice, as SmartTwin answers the steps that would make it so. The small measures
 * they tick are calculated all the same.
 */
final class FormSmallMeasuresTest extends TestCase
{
    use RefreshDatabase;

    public bool $seed = true;
    public string $seeder = DatabaseSeeder::class;

    private Building $building;

    protected function setUp(): void
    {
        parent::setUp();

        $cooperation = Cooperation::factory()->create();
        $cooperation->scans()->attach(Scan::quick()->id);

        $resident = User::factory()->withAccount()->asResident()
            ->create(['cooperation_id' => $cooperation->id, 'allow_access' => true]);
        $this->building = Building::factory()->create(['user_id' => $resident->id]);

        $this->actingAs($resident->account);
        HoomdossierSession::setCooperation($cooperation);
        HoomdossierSession::setHoomdossierSessions(
            $this->building,
            InputSource::resident(),
            InputSource::resident(),
            Role::findByName(RoleHelper::ROLE_RESIDENT),
        );
    }

    private function tickLedLight(): void
    {
        $step = Step::findByShort('small-measures');
        $subStep = SubStep::where('step_id', $step->id)
            ->whereHas('toolQuestions', fn ($query) => $query->where('short', 'apply-led-light'))
            ->firstOrFail();

        Livewire::test(Form::class, ['scan' => Scan::quick(), 'step' => $step, 'subStep' => $subStep])
            ->set('filledInAnswers.apply-led-light', 'want-to')
            // Required without SmartTwin.
            ->set('filledInAnswers.turn-off-lights', 'not-interested')
            ->call('save')
            ->assertHasNoErrors();
    }

    /**
     * Completes a second sub step of the small measures for the first time.
     */
    private function saveEquipment(): void
    {
        $step = Step::findByShort('small-measures');
        $subStep = SubStep::where('step_id', $step->id)
            ->whereHas('toolQuestions', fn ($query) => $query->where('short', 'replace-old-equipment'))
            ->firstOrFail();

        Livewire::test(Form::class, ['scan' => Scan::quick(), 'step' => $step, 'subStep' => $subStep])
            ->set('filledInAnswers.replace-old-equipment', 'want-to')
            ->call('save')
            ->assertHasNoErrors();
    }

    /**
     * @return array<int, string>
     */
    private function smallMeasures(InputSource $inputSource): array
    {
        return $this->measures($inputSource)
            ->where('step_id', Step::findByShort('small-measures')->id)
            ->pluck('short')
            ->values()
            ->all();
    }

    /**
     * The calculated measures that are not a small measure.
     *
     * @return array<int, string>
     */
    private function otherMeasures(InputSource $inputSource): array
    {
        return $this->measures($inputSource)
            ->where('step_id', '!=', Step::findByShort('small-measures')->id)
            ->pluck('short')
            ->values()
            ->all();
    }

    /**
     * @return \Illuminate\Support\Collection<int, array{short: string, step_id: ?int}>
     */
    private function measures(InputSource $inputSource): \Illuminate\Support\Collection
    {
        return UserActionPlanAdvice::withoutGlobalScopes()
            ->where('user_id', $this->building->user_id)
            ->where('input_source_id', $inputSource->id)
            ->where('user_action_plan_advisable_type', MeasureApplication::class)
            ->get()
            ->map(fn (UserActionPlanAdvice $advice) => [
                'short'   => MeasureApplication::find($advice->user_action_plan_advisable_id)->short,
                'step_id' => $advice->step_id,
            ]);
    }

    public function test_a_small_measure_is_calculated_before_smart_twins_first_advice(): void
    {
        config()->set('hoomdossier.services.smarttwin.enabled', true);

        $this->tickLedLight();

        $this->assertSame(['save-energy-with-light'], $this->smallMeasures(InputSource::resident()));
        $this->assertSame(['save-energy-with-light'], $this->smallMeasures(InputSource::master()));
    }

    /**
     * The calculated small measures must not make the quick scan calculable: the next sub step would
     * then start a full recalculation and put measures such as crack sealing and ventilation on the
     * woonplan before SmartTwin has advised.
     */
    public function test_small_measures_before_smart_twins_first_advice_do_not_start_the_rest_of_the_calculation(): void
    {
        config()->set('hoomdossier.services.smarttwin.enabled', true);
        $this->building->user->cooperation->scans()->attach(Scan::expert()->id);

        $this->tickLedLight();
        $this->saveEquipment();

        $this->assertSame([], $this->otherMeasures(InputSource::resident()));
        $this->assertSame([], $this->otherMeasures(InputSource::master()));
        $this->assertContains('save-energy-with-light', $this->smallMeasures(InputSource::master()));

        $building = $this->building->fresh();
        $woonplanService = WoonplanService::init($building)->scan(Scan::quick());
        $this->assertFalse(BuildingService::init($building)->canCalculate(Scan::quick()));
        $this->assertFalse($woonplanService->hasPlan());
        $this->assertFalse($woonplanService->canEnterExpertScan($building->user->cooperation));
    }

    public function test_after_smart_twins_first_advice_the_small_measures_count_again(): void
    {
        config()->set('hoomdossier.services.smarttwin.enabled', true);

        $this->tickLedLight();

        $measure = MeasureApplication::findByShort('cavity-wall-insulation');
        UserActionPlanAdvice::withoutEvents(fn () => UserActionPlanAdvice::withoutGlobalScopes()->forceCreate([
            'user_id'                         => $this->building->user_id,
            'input_source_id'                 => InputSource::resident()->id,
            'user_action_plan_advisable_type' => MeasureApplication::class,
            'user_action_plan_advisable_id'   => $measure->id,
            'step_id'                         => $measure->step_id,
            'category'                        => 'to-do',
            'visible'                         => true,
            'source'                          => AdviceSource::SMART_TWIN->value,
        ]));

        // Only the small measure is in master, so it is what makes the scan calculable.
        $woonplanService = WoonplanService::init($this->building->fresh())->scan(Scan::quick());
        $this->assertTrue($woonplanService->buildingHasMeasureApplications());
    }

    public function test_without_smart_twin_an_unfinished_quick_scan_still_calculates_nothing(): void
    {
        config()->set('hoomdossier.services.smarttwin.enabled', false);

        $this->tickLedLight();

        $this->assertSame([], $this->smallMeasures(InputSource::master()));
    }
}
