<?php

namespace Tests\Feature\app\Jobs\SmartTwin;

use App\Enums\AdviceSource;
use App\Helpers\MediaHelper;
use App\Jobs\SmartTwin\StoreReleasedWoonplanReport;
use App\Models\Building;
use App\Models\FileStorage;
use App\Models\FileType;
use App\Models\InputSource;
use App\Models\Media;
use App\Models\MeasureApplication;
use App\Models\SmartTwinPackage;
use App\Models\User;
use App\Models\UserActionPlanAdvice;
use App\Services\Models\BuildingStatusService;
use App\Services\SmartTwin\WoonplanLock;
use App\Services\UserReport;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * When a coach releases a SmartTwin woonplan by setting the building status to "Uitgevoerd", its
 * report is kept with the building's files and as the coach's report (decision
 * 2026-10-16-smarttwin-rapport-bij-vrijgeven).
 */
final class StoreReleasedWoonplanReportTest extends TestCase
{
    use RefreshDatabase;

    public $seed = true;
    public $seeder = DatabaseSeeder::class;

    private User $user;
    private Building $building;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        config()->set('hoomdossier.services.smarttwin.enabled', true);
        Storage::fake('uploads');
        Storage::fake('downloads');

        $this->user = User::first();
        $this->building = $this->user->building;
    }

    private function switchToSmartTwin(): void
    {
        $measure = MeasureApplication::findByShort('cavity-wall-insulation');

        foreach ([InputSource::resident(), InputSource::coach(), InputSource::master()] as $inputSource) {
            UserActionPlanAdvice::withoutEvents(fn () => UserActionPlanAdvice::withoutGlobalScopes()->forceCreate([
                'user_id'                         => $this->user->id,
                'input_source_id'                 => $inputSource->id,
                'user_action_plan_advisable_type' => MeasureApplication::class,
                'user_action_plan_advisable_id'   => $measure->id,
                'step_id'                         => $measure->step_id,
                'costs'                           => ['from' => null, 'to' => 1000],
                'category'                        => 'to-do',
                'visible'                         => true,
                'source'                          => AdviceSource::SMART_TWIN->value,
            ]));
        }

        SmartTwinPackage::create([
            'building_id'             => $this->building->id,
            'input_source_id'         => InputSource::coach()->id,
            'measure_application_ids' => [$measure->id],
            'imported_at'             => now(),
        ]);
    }

    private function reports()
    {
        return $this->building->fresh()->getMedia(MediaHelper::REPORT);
    }

    private function coachReports()
    {
        return FileType::findByShort('pdf-report')->files()->withExpired()
            ->forBuilding($this->building)->forInputSource(InputSource::coach())->get();
    }

    public function test_releasing_a_smart_twin_woonplan_stores_its_report(): void
    {
        Bus::fake([StoreReleasedWoonplanReport::class]);
        $this->switchToSmartTwin();
        app(WoonplanLock::class)->lock($this->building);

        app(BuildingStatusService::class)->forBuilding($this->building)->setStatus('executed');

        $this->assertFalse(app(WoonplanLock::class)->isLocked($this->building));
        Bus::assertDispatched(StoreReleasedWoonplanReport::class, fn ($job) => $job->building->is($this->building));
    }

    public function test_another_status_stores_nothing(): void
    {
        Bus::fake([StoreReleasedWoonplanReport::class]);
        $this->switchToSmartTwin();

        app(BuildingStatusService::class)->forBuilding($this->building)->setStatus('in_progress');

        Bus::assertNotDispatched(StoreReleasedWoonplanReport::class);
    }

    public function test_a_woonplan_that_is_not_smart_twins_stores_nothing(): void
    {
        Bus::fake([StoreReleasedWoonplanReport::class]);

        app(BuildingStatusService::class)->forBuilding($this->building)->setStatus('executed');

        Bus::assertNotDispatched(StoreReleasedWoonplanReport::class);
    }

    public function test_the_report_goes_with_the_files_and_replaces_the_coachs_report(): void
    {
        $this->switchToSmartTwin();
        Carbon::setTestNow('2026-10-16 10:00');
        // A report the coach made with the button before.
        $earlier = FileStorage::create([
            'building_id'     => $this->building->id,
            'cooperation_id'  => $this->user->cooperation_id,
            'input_source_id' => InputSource::coach()->id,
            'file_type_id'    => FileType::findByShort('pdf-report')->id,
            'filename'        => 'earlier.pdf',
        ]);
        Storage::disk('downloads')->put('earlier.pdf', 'old');

        (new StoreReleasedWoonplanReport($this->building))->handle(app(UserReport::class));

        /** @var Media $media */
        $media = $this->reports()->sole();
        $this->assertSame('woonplan-rapportage-16-10-2026', $media->filename);
        $this->assertSame('pdf', $media->extension);
        $this->assertTrue($media->custom_properties['share_with_cooperation']);
        $this->assertSame('2026-10-16', $media->custom_properties[StoreReleasedWoonplanReport::MADE_ON_RELEASE]);
        $this->assertSame(InputSource::coach()->id, $media->input_source_id);
        // The resident sees every file of their own building (MediaPolicy::before()).
        $this->assertTrue($media->ownedBy($this->user));
        $this->assertStringStartsWith('%PDF', Storage::disk('uploads')->get($media->getDiskPath()));

        // The coach's report, as Econobis picks it up; the earlier one is gone.
        $this->assertNull(FileStorage::withExpired()->find($earlier->id));
        Storage::disk('downloads')->assertMissing('earlier.pdf');
        $coachReport = $this->coachReports()->sole();
        $this->assertFalse($coachReport->is_being_processed);
        $this->assertStringStartsWith('%PDF', Storage::disk('downloads')->get($coachReport->filename));
    }

    public function test_every_release_keeps_a_report_of_its_own(): void
    {
        $this->switchToSmartTwin();

        Carbon::setTestNow('2026-10-16 10:00');
        (new StoreReleasedWoonplanReport($this->building))->handle(app(UserReport::class));
        Carbon::setTestNow('2026-12-01 10:00');
        (new StoreReleasedWoonplanReport($this->building))->handle(app(UserReport::class));

        $this->assertSame(
            ['woonplan-rapportage-01-12-2026', 'woonplan-rapportage-16-10-2026'],
            $this->reports()->pluck('filename')->sort()->values()->all(),
        );
        $this->assertCount(1, $this->coachReports());
    }

    public function test_nothing_is_stored_once_smart_twin_is_switched_off_again(): void
    {
        $this->switchToSmartTwin();
        $this->user->cooperation->update(['smarttwin_enabled' => false]);

        (new StoreReleasedWoonplanReport($this->building))->handle(app(UserReport::class));

        $this->assertCount(0, $this->reports());
        $this->assertCount(0, $this->coachReports());
    }
}
