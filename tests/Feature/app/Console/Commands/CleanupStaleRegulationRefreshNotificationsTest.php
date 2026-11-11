<?php

namespace Tests\Feature\app\Console\Commands;

use App\Jobs\RecalculateStepForUser;
use App\Jobs\RefreshRegulationsForUserActionPlanAdvice;
use App\Models\Building;
use App\Models\Notification;
use App\Models\User;
use App\Services\Models\NotificationService;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Tests\TestCase;

final class CleanupStaleRegulationRefreshNotificationsTest extends TestCase
{
    use RefreshDatabase;

    public bool $seed = true;
    public string $seeder = DatabaseSeeder::class;

    public function test_only_stale_regulation_refresh_notifications_are_deleted(): void
    {
        $building = Building::factory()->create(['user_id' => User::factory()->create()->id]);

        $this->travelTo(Carbon::now()->subHours(3));
        $this->setActive($building, RefreshRegulationsForUserActionPlanAdvice::class);
        $this->setActive($building, RecalculateStepForUser::class);
        $this->travelBack();

        $this->setActive($building, RefreshRegulationsForUserActionPlanAdvice::class);
        $recentUuid = Notification::allInputSources()->latest('id')->value('uuid');

        // Each notification also has a copy for the master input source.
        $this->assertSame(6, Notification::allInputSources()->count());

        $this->artisan('notifications:cleanup-stale-regulation-refreshes')->assertSuccessful();

        $remaining = Notification::allInputSources()->get();
        $this->assertCount(4, $remaining);
        $this->assertSame(
            [RecalculateStepForUser::class, RefreshRegulationsForUserActionPlanAdvice::class],
            $remaining->pluck('type')->unique()->sort()->values()->all()
        );
        $this->assertSame(
            [$recentUuid],
            $remaining->where('type', RefreshRegulationsForUserActionPlanAdvice::class)->pluck('uuid')->unique()->values()->all()
        );
    }

    private function setActive(Building $building, string $type): void
    {
        NotificationService::init()
            ->forBuilding($building)
            ->setType($type)
            ->setActive([(string) Str::uuid()]);
    }
}
