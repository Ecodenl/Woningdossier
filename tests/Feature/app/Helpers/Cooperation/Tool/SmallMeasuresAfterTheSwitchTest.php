<?php

namespace Tests\Feature\app\Helpers\Cooperation\Tool;

use App\Enums\AdviceSource;
use App\Helpers\Cooperation\Tool\SmallMeasureHelper;
use App\Models\Building;
use App\Models\Cooperation;
use App\Models\InputSource;
use App\Models\MeasureApplication;
use App\Models\Scan;
use App\Models\ToolQuestion;
use App\Models\User;
use App\Models\UserActionPlanAdvice;
use App\Services\ToolQuestionService;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Small measures stay Hoomdossier's after the switch, calculated from the answers of whoever saved
 * them. That outcome counts for resident, coach and master alike; before the switch every input
 * source keeps its own, as it always did.
 */
final class SmallMeasuresAfterTheSwitchTest extends TestCase
{
    use RefreshDatabase;

    public $seed = true;
    public $seeder = DatabaseSeeder::class;

    private User $user;
    private Building $building;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('hoomdossier.services.smarttwin.enabled', true);

        $cooperation = Cooperation::factory()->create();
        $cooperation->scans()->attach(Scan::findByShort(Scan::QUICK)->id);

        $this->user = User::factory()->withAccount()->asResident()->create(['cooperation_id' => $cooperation->id]);
        $this->building = Building::factory()->create(['user_id' => $this->user->id]);
    }

    private function switchToSmartTwin(): void
    {
        $measure = MeasureApplication::findByShort('cavity-wall-insulation');

        UserActionPlanAdvice::withoutGlobalScopes()->create([
            'user_id'                         => $this->user->id,
            'input_source_id'                 => InputSource::resident()->id,
            'user_action_plan_advisable_type' => MeasureApplication::class,
            'user_action_plan_advisable_id'   => $measure->id,
            'step_id'                         => $measure->step_id,
            'costs'                           => ['from' => null, 'to' => 1000],
            'source'                          => AdviceSource::SMART_TWIN,
        ]);
    }

    private function residentAnswersLed(string $answer): void
    {
        ToolQuestionService::init()
            ->toolQuestion(ToolQuestion::findByShort('apply-led-light'))
            ->building($this->building)
            ->currentInputSource(InputSource::resident())
            ->save($answer);

        (new SmallMeasureHelper($this->user->fresh(), InputSource::resident()))->createValues()->createAdvices();
    }

    /**
     * @return array<int, string> The input sources with a row for the LED measure.
     */
    private function ledRows(): array
    {
        return UserActionPlanAdvice::withoutGlobalScopes()
            ->where('user_id', $this->user->id)
            ->where('user_action_plan_advisable_id', MeasureApplication::findByShort('save-energy-with-light')->id)
            ->get()
            ->map(fn (UserActionPlanAdvice $row) => InputSource::find($row->input_source_id)->short)
            ->sort()
            ->values()
            ->all();
    }

    public function test_after_the_switch_the_resident_calculation_counts_for_all_three(): void
    {
        $this->switchToSmartTwin();

        $this->residentAnswersLed('want-to');

        $this->assertSame(['coach', 'master', 'resident'], $this->ledRows());
    }

    public function test_after_the_switch_a_dropped_measure_goes_for_all_three(): void
    {
        $this->switchToSmartTwin();
        $this->residentAnswersLed('want-to');

        $this->residentAnswersLed('not-interested');

        $this->assertSame([], $this->ledRows());
    }

    public function test_before_the_switch_every_input_source_keeps_its_own(): void
    {
        $this->residentAnswersLed('want-to');

        $this->assertSame(['master', 'resident'], $this->ledRows());
    }
}
