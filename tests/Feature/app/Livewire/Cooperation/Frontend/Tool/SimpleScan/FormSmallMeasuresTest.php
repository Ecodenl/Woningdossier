<?php

namespace Tests\Feature\app\Livewire\Cooperation\Frontend\Tool\SimpleScan;

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
     * @return array<int, string>
     */
    private function smallMeasures(InputSource $inputSource): array
    {
        return UserActionPlanAdvice::withoutGlobalScopes()
            ->where('user_id', $this->building->user_id)
            ->where('input_source_id', $inputSource->id)
            ->where('step_id', Step::findByShort('small-measures')->id)
            ->get()
            ->map(fn (UserActionPlanAdvice $advice) => MeasureApplication::find($advice->user_action_plan_advisable_id)->short)
            ->all();
    }

    public function test_a_small_measure_is_calculated_before_smart_twins_first_advice(): void
    {
        config()->set('hoomdossier.services.smarttwin.enabled', true);

        $this->tickLedLight();

        $this->assertSame(['save-energy-with-light'], $this->smallMeasures(InputSource::resident()));
        $this->assertSame(['save-energy-with-light'], $this->smallMeasures(InputSource::master()));
    }

    public function test_without_smart_twin_an_unfinished_quick_scan_still_calculates_nothing(): void
    {
        config()->set('hoomdossier.services.smarttwin.enabled', false);

        $this->tickLedLight();

        $this->assertSame([], $this->smallMeasures(InputSource::master()));
    }
}
