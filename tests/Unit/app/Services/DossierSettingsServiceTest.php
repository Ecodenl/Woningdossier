<?php

namespace Tests\Unit\app\Services;

use App\Models\Building;
use App\Models\InputSource;
use App\Models\User;
use App\Services\DossierSettingsService;
use App\Services\MappingService;
use Carbon\Carbon;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\InputSourcesTableSeeder;
use Database\Seeders\RoleTableSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

final class DossierSettingsServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $building;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(InputSourcesTableSeeder::class);

        $this->building = Building::factory()->create(['user_id' => User::factory()->create()]);
    }
    public function test_that_datetime_before_done_at_returns_true_on_is_done_after(): void
    {
        $resident = InputSource::resident();
        // this doesn't matter to the code, but this makes the test more read/understandable.
        $type = 'RESET_ACCOUNT';

        // so this should be resolved from somewhere but that isn't relevant here
        // we will simulate that a job has been queued 10 minutes ago
        $jobQueuedAt = Carbon::now()->subMinutes(10);

        // now simulate that a reset just has been done.
        app(DossierSettingsService::class)
            ->forBuilding($this->building)
            ->forInputSource($resident)
            ->forType($type)
            ->justDone();

        // and check if the action is done after the job queueing
        $actionDoneAfterJobQueuedAt = app(DossierSettingsService::class)
            ->forBuilding($this->building)
            ->forInputSource($resident)
            ->forType($type)
            ->isDoneAfter($jobQueuedAt);

        $this->assertTrue($actionDoneAfterJobQueuedAt);
    }
    public function test_that_is_done_after_returns_false_on_empty_dossier_setting(): void
    {
        $resident = InputSource::resident();
        // this doesn't matter to the code, but this makes the test more read/understandable.
        $type = 'RESET_ACCOUNT';

        // so this should be resolved from somewhere but that isn't relevant here
        // we will simulate that a job has been queued 10 minutes ago
        $jobQueuedAt = Carbon::now()->subMinutes(10);

        // and check if the action is done after the job queueing
        $actionDoneAfterJobQueuedAt = app(DossierSettingsService::class)
            ->forBuilding($this->building)
            ->forInputSource($resident)
            ->forType($type)
            ->isDoneAfter($jobQueuedAt);

        $this->assertFalse($actionDoneAfterJobQueuedAt);
    }

    public function test_that_datetime_after_done_at_returns_false_on_is_done_after(): void
    {
        $resident = InputSource::resident();
        // this doesn't matter to the code, but this makes the test more read/understandable.
        $type = 'RESET_ACCOUNT';

        // so this should be resolved from somewhere but that isn't relevant here
        // we will simulate that a job has been queued 10 minutes ago
        $jobQueuedAt = Carbon::now()->addMinutes(10);

        // now simulate that a reset just has been done.
        app(DossierSettingsService::class)
            ->forBuilding($this->building)
            ->forInputSource($resident)
            ->forType($type)
            ->justDone();

        // and check if the action is done after the job queueing
        $actionDoneAfterJobQueuedAt = app(DossierSettingsService::class)
            ->forBuilding($this->building)
            ->forInputSource($resident)
            ->forType($type)
            ->isDoneAfter($jobQueuedAt);

        $this->assertFalse($actionDoneAfterJobQueuedAt);
    }
}
