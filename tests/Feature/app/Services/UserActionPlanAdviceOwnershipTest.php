<?php

namespace Tests\Feature\app\Services;

use App\Enums\AdviceSource;
use App\Models\InputSource;
use App\Models\MeasureApplication;
use App\Models\Step;
use App\Models\User;
use App\Models\UserActionPlanAdvice;
use App\Services\UserActionPlanAdviceService;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * An advice normally belongs to the calculation: thrown away and rebuilt whenever its step is
 * recalculated. An advice that came from elsewhere may not be, and the calculation may not put a
 * second card for the same measure next to it.
 *
 * Without a source set nothing changes, which is what a dossier with no external advice looks like.
 */
final class UserActionPlanAdviceOwnershipTest extends TestCase
{
    use RefreshDatabase;

    public $seed = true;
    public $seeder = DatabaseSeeder::class;

    private User $user;
    private InputSource $coach;
    private Step $step;
    private MeasureApplication $measure;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::first();
        $this->coach = InputSource::findByShort(InputSource::COACH_SHORT);
        $this->step = Step::findByShort('wall-insulation');
        $this->measure = MeasureApplication::findByShort('cavity-wall-insulation');
    }

    private function advice(?AdviceSource $source, ?MeasureApplication $measure = null): UserActionPlanAdvice
    {
        $advice = new UserActionPlanAdvice([
            'user_id'                         => $this->user->id,
            'input_source_id'                 => $this->coach->id,
            'user_action_plan_advisable_type' => MeasureApplication::class,
            'user_action_plan_advisable_id'   => ($measure ?? $this->measure)->id,
            'step_id'                         => $this->step->id,
            'costs'                           => ['from' => null, 'to' => 1622.65],
            'source'                          => $source,
        ]);

        $advice->save();

        return $advice;
    }

    private function adviceCount(?MeasureApplication $measure = null): int
    {
        return UserActionPlanAdvice::withoutGlobalScopes()
            ->where('user_id', $this->user->id)
            ->where('input_source_id', $this->coach->id)
            ->where('user_action_plan_advisable_id', ($measure ?? $this->measure)->id)
            ->count();
    }

    public function test_clearing_a_step_leaves_an_externally_owned_advice_alone(): void
    {
        $this->advice(AdviceSource::SMART_TWIN);

        UserActionPlanAdviceService::clearForStep($this->user, $this->coach, $this->step);

        $this->assertSame(1, $this->adviceCount());
    }

    public function test_clearing_a_step_still_removes_a_calculated_advice(): void
    {
        $this->advice(null);

        UserActionPlanAdviceService::clearForStep($this->user, $this->coach, $this->step);

        $this->assertSame(0, $this->adviceCount());
    }

    public function test_the_calculation_does_not_add_a_second_card_for_an_owned_measure(): void
    {
        $this->advice(AdviceSource::SMART_TWIN);

        // What a helper does after clearing its step: build the advice it calculated.
        $this->advice(null);

        $this->assertSame(1, $this->adviceCount());
        $this->assertSame(
            AdviceSource::SMART_TWIN,
            UserActionPlanAdvice::withoutGlobalScopes()->where('user_id', $this->user->id)->first()->source,
        );
    }

    public function test_ownership_only_covers_the_measure_it_was_set_for(): void
    {
        // The other measures of the same step keep being calculated; SmartTwin prices one of them,
        // while maintenance measures like repair-joint come from the facade's condition.
        $repairJoint = MeasureApplication::findByShort('repair-joint');

        $this->advice(AdviceSource::SMART_TWIN);
        $this->advice(null, $repairJoint);

        $this->assertSame(1, $this->adviceCount());
        $this->assertSame(1, $this->adviceCount($repairJoint));
    }

    public function test_an_externally_owned_advice_can_be_replaced_by_another(): void
    {
        // A second SmartTwin scan maps again and must be able to write its new figures.
        $this->advice(AdviceSource::SMART_TWIN);

        UserActionPlanAdvice::withoutGlobalScopes()
            ->where('user_id', $this->user->id)
            ->whereNotNull('source')
            ->delete();

        $this->advice(AdviceSource::SMART_TWIN);

        $this->assertSame(1, $this->adviceCount());
    }

    public function test_an_advice_without_a_source_behaves_exactly_as_before(): void
    {
        $this->advice(null);

        $this->assertSame(1, $this->adviceCount());
        $this->assertNull(
            UserActionPlanAdvice::withoutGlobalScopes()->where('user_id', $this->user->id)->first()->source,
        );
    }
}
