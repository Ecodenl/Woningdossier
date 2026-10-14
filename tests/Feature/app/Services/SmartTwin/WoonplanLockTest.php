<?php

namespace Tests\Feature\app\Services\SmartTwin;

use App\Enums\AdviceSource;
use App\Enums\SmartTwin\WoonplanState;
use App\Models\Building;
use App\Models\InputSource;
use App\Models\MeasureApplication;
use App\Models\User;
use App\Models\UserActionPlanAdvice;
use App\Services\Models\BuildingStatusService;
use App\Services\SmartTwin\WoonplanLock;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * The resident's woonplan while a coach works on it in SmartTwin: locked from the started advice
 * until the coach sets the building to "Uitgevoerd".
 */
final class WoonplanLockTest extends TestCase
{
    use RefreshDatabase;

    public $seed = true;
    public $seeder = DatabaseSeeder::class;

    private Building $building;
    private WoonplanLock $lock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->building = User::first()->building;
        $this->lock = app(WoonplanLock::class);
    }

    private function coachAdvice(?AdviceSource $source): void
    {
        $measure = MeasureApplication::findByShort('floor-insulation');

        UserActionPlanAdvice::withoutGlobalScopes()->create([
            'user_id'                         => $this->building->user_id,
            'input_source_id'                 => InputSource::findByShort(InputSource::COACH_SHORT)->id,
            'user_action_plan_advisable_type' => MeasureApplication::class,
            'user_action_plan_advisable_id'   => $measure->id,
            'step_id'                         => $measure->step_id,
            'costs'                           => ['from' => null, 'to' => 3613.26],
            'source'                          => $source,
        ]);
    }

    private function setStatus(string $short): void
    {
        app(BuildingStatusService::class)->forBuilding($this->building)->setStatus($short);
    }

    public function test_a_woonplan_without_the_setting_is_open(): void
    {
        $this->assertFalse($this->lock->isLocked($this->building));
        $this->assertSame(WoonplanState::OPEN, $this->lock->state($this->building));
    }

    public function test_locked_without_a_coach_plan_is_awaiting_the_coach(): void
    {
        $this->lock->lock($this->building);

        $this->assertSame(WoonplanState::AWAITING_COACH, $this->lock->state($this->building));
        $this->assertFalse($this->lock->state($this->building)->residentMayEdit());
    }

    public function test_locked_with_a_coach_plan_shows_it_locked(): void
    {
        $this->coachAdvice(AdviceSource::SMART_TWIN);
        $this->lock->lock($this->building);

        $this->assertSame(WoonplanState::LOCKED, $this->lock->state($this->building));
    }

    public function test_a_coach_advice_from_before_smarttwin_is_no_coach_plan(): void
    {
        $this->coachAdvice(null);
        $this->lock->lock($this->building);

        $this->assertSame(WoonplanState::AWAITING_COACH, $this->lock->state($this->building));
    }

    public function test_setting_the_building_to_executed_releases_the_woonplan(): void
    {
        $this->lock->lock($this->building);

        $this->setStatus('executed');

        $this->assertSame(WoonplanState::OPEN, $this->lock->state($this->building->fresh()));
    }

    public function test_another_status_keeps_it_locked(): void
    {
        $this->lock->lock($this->building);

        $this->setStatus('in_progress');

        $this->assertTrue($this->lock->isLocked($this->building->fresh()));
    }

    public function test_a_new_advice_after_the_release_locks_it_again(): void
    {
        $this->lock->lock($this->building);
        $this->setStatus('executed');

        $this->lock->lock($this->building);

        $this->assertTrue($this->lock->isLocked($this->building->fresh()));
    }
}
