<?php

namespace Tests\Feature\app\Jobs;

use App\Jobs\RefreshRegulationsForUserActionPlanAdvice;
use App\Models\Building;
use App\Models\CustomMeasureApplication;
use App\Models\InputSource;
use App\Models\Notification;
use App\Models\User;
use App\Models\UserActionPlanAdvice;
use App\Services\Models\NotificationService;
use Database\Seeders\InputSourcesTableSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class RefreshRegulationsForUserActionPlanAdviceTest extends TestCase
{
    use RefreshDatabase;

    private Building $building;
    private UserActionPlanAdvice $advice;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(InputSourcesTableSeeder::class);

        $user = User::factory()->create();
        $this->building = Building::factory()->create(['user_id' => $user->id]);

        $coach = InputSource::findByShort(InputSource::COACH_SHORT);
        $customMeasureApplication = CustomMeasureApplication::factory()->create([
            'building_id' => $this->building->id,
            'input_source_id' => $coach->id,
        ]);

        $this->advice = UserActionPlanAdvice::factory()->create([
            'user_id' => $user->id,
            'input_source_id' => $coach->id,
            'user_action_plan_advisable_type' => CustomMeasureApplication::class,
            'user_action_plan_advisable_id' => $customMeasureApplication->id,
        ]);
    }

    public function test_regulations_are_refreshed_and_notification_is_removed(): void
    {
        // The building has no municipality, so there is no Verbeterjehuis request and the advice has no regulations.
        $this->advice->update(['subsidy_available' => true, 'loan_available' => true]);

        dispatch($this->queueJob());

        $this->advice->refresh();
        $this->assertFalse($this->advice->subsidy_available);
        $this->assertFalse($this->advice->loan_available);
        $this->assertNotificationIsRemoved();
    }

    public function test_notification_is_removed_when_the_advice_is_deleted_before_the_job_runs(): void
    {
        $job = $this->queueJob();

        // A recalculation deletes and recreates the advices while the refresh is still queued.
        $this->advice->delete();

        dispatch($job);

        $this->assertNotificationIsRemoved();
    }

    private function queueJob(): RefreshRegulationsForUserActionPlanAdvice
    {
        $job = new RefreshRegulationsForUserActionPlanAdvice($this->advice);

        // Same as the callers: the notification is set without input source.
        NotificationService::init()
            ->forBuilding($this->building)
            ->setType(RefreshRegulationsForUserActionPlanAdvice::class)
            ->setActive([$job->uuid]);

        $this->assertTrue($this->isRefreshing());

        return $job;
    }

    private function assertNotificationIsRemoved(): void
    {
        $this->assertFalse($this->isRefreshing());
        $this->assertSame(0, Notification::allInputSources()->forBuilding($this->building)->count());
    }

    private function isRefreshing(): bool
    {
        return NotificationService::init()
            ->forBuilding($this->building)
            ->setType(RefreshRegulationsForUserActionPlanAdvice::class)
            ->isActive();
    }
}
