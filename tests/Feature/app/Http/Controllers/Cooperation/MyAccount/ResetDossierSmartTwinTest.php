<?php

namespace Tests\Feature\app\Http\Controllers\Cooperation\MyAccount;

use App\Enums\AdviceSource;
use App\Enums\SmartTwin\EventType;
use App\Helpers\HoomdossierSession;
use App\Helpers\RoleHelper;
use App\Jobs\SmartTwin\Out\GetBuildingData;
use App\Models\Building;
use App\Models\Cooperation;
use App\Models\InputSource;
use App\Models\MeasureApplication;
use App\Models\Role;
use App\Models\Scan;
use App\Models\SmartTwinPackage;
use App\Models\Step;
use App\Models\User;
use App\Models\UserActionPlanAdvice;
use App\Services\Lvbag\BagService;
use App\Services\Lvbag\Payloads\AddressExpanded;
use App\Services\SmartTwin\Api\UserRole;
use App\Services\SmartTwin\WoonplanLock;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Testing\TestResponse;
use Mockery\MockInterface;
use Tests\TestCase;

/**
 * A reset takes the woonplan back to the start: what SmartTwin left on the building goes with the
 * data of the input source that is reset, so the resident is invited to do the check again.
 */
final class ResetDossierSmartTwinTest extends TestCase
{
    use RefreshDatabase;

    public bool $seed = true;
    public string $seeder = DatabaseSeeder::class;

    private Cooperation $cooperation;
    private Building $building;
    private Scan $scan;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        config()->set('hoomdossier.services.smarttwin.enabled', true);
        config()->set('hoomdossier.skip_woonplan_guard', false);
        Bus::fake([GetBuildingData::class]);
        $this->mock(BagService::class, fn (MockInterface $mock) => $mock
            ->shouldReceive('addressExpanded')
            ->andReturn(new AddressExpanded(['endpoint_failure' => true]))
        );

        $this->cooperation = Cooperation::factory()->create(['smarttwin_enabled' => true])->fresh();
        $this->scan = Scan::findByShort(Scan::QUICK);
        $this->cooperation->scans()->attach($this->scan->id);

        $resident = User::factory()->withAccount()->asResident()
            ->create(['cooperation_id' => $this->cooperation->id, 'allow_access' => true]);
        $this->building = Building::factory()->create(['user_id' => $resident->id]);
        $resident->account->linkSmartTwinUser('st-user-1', UserRole::Resident);

        $this->actingAs($resident->account->fresh());
        HoomdossierSession::setCooperation($this->cooperation);
        HoomdossierSession::setHoomdossierSessions(
            $this->building,
            InputSource::resident(),
            InputSource::resident(),
            Role::findByName(RoleHelper::ROLE_RESIDENT),
        );
    }

    /**
     * The plan a coach delivered: a SmartTwin card for everyone, and a package from the coach's flow.
     */
    private function coachPlan(): void
    {
        $measure = MeasureApplication::findByShort('cavity-wall-insulation');

        foreach ([InputSource::resident(), InputSource::coach(), InputSource::master()] as $inputSource) {
            UserActionPlanAdvice::withoutEvents(fn () => UserActionPlanAdvice::withoutGlobalScopes()->forceCreate([
                'user_id'                         => $this->building->user_id,
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

    private function openCallbacks(): void
    {
        $this->building->setSmartTwinCallback(EventType::RESIDENT_SCAN_FINISHED, ['DossierId' => 'resident']);
        $this->building->setSmartTwinCallback(EventType::COACH_SCAN_FINISHED, ['DossierId' => 'coach']);
        $this->building->saveQuietly();
    }

    /**
     * @param  array<int, InputSource>  $inputSources
     */
    private function reset(array $inputSources): TestResponse
    {
        return $this->post(route('cooperation.my-account.settings.reset-file', ['cooperation' => $this->cooperation]), [
            'input_sources' => ['id' => array_map(fn (InputSource $inputSource) => $inputSource->id, $inputSources)],
        ]);
    }

    private function visitWoonplan(): TestResponse
    {
        return $this->get(route('cooperation.frontend.tool.simple-scan.my-plan.index', [
            'cooperation' => $this->cooperation,
            'scan'        => $this->scan,
        ]));
    }

    public function test_a_reset_with_the_coach_invites_the_resident_to_do_the_check_again(): void
    {
        $this->coachPlan();
        $this->openCallbacks();

        $this->reset([InputSource::resident(), InputSource::coach()])->assertRedirect();

        $response = $this->visitWoonplan();

        $response->assertOk();
        $response->assertSee(__('cooperation/frontend/tool.my-plan.start-check.body'), false);
        $response->assertDontSee(__('cooperation/frontend/tool.my-plan.awaiting-advice.body'), false);
        $response->assertDontSee(__('cooperation/frontend/tool.my-plan.lock.done.text'), false);
        $response->assertDontSee(__('cooperation/frontend/tool.my-plan.smarttwin.report-first.title'), false);
    }

    public function test_a_reset_with_the_coach_clears_what_smart_twin_left_on_the_building(): void
    {
        $this->coachPlan();
        $this->openCallbacks();
        app(WoonplanLock::class)->lock($this->building);

        $this->reset([InputSource::resident(), InputSource::coach()])->assertRedirect();

        $building = $this->building->fresh();
        $this->assertSame([], $building->getSmartTwinCallbacks());
        $this->assertNull($building->smartTwinPackage()->first());
        $this->assertFalse(app(WoonplanLock::class)->isLocked($building));
    }

    public function test_small_measures_answered_after_a_reset_keep_the_invitation(): void
    {
        $this->coachPlan();

        $this->reset([InputSource::resident(), InputSource::coach()])->assertRedirect();

        $measure = MeasureApplication::where('step_id', Step::findByShort('small-measures')->id)->first();
        UserActionPlanAdvice::withoutEvents(fn () => UserActionPlanAdvice::withoutGlobalScopes()->forceCreate([
            'user_id'                         => $this->building->user_id,
            'input_source_id'                 => InputSource::master()->id,
            'user_action_plan_advisable_type' => MeasureApplication::class,
            'user_action_plan_advisable_id'   => $measure->id,
            'step_id'                         => $measure->step_id,
            'costs'                           => ['from' => null, 'to' => null],
            'category'                        => 'to-do',
            'visible'                         => true,
        ]));

        $response = $this->visitWoonplan();

        $response->assertOk();
        $response->assertSee(__('cooperation/frontend/tool.my-plan.start-check.body'), false);
        $response->assertDontSee(__('cooperation/frontend/tool.my-plan.lock.done.text'), false);
        $response->assertDontSee(__('cooperation/frontend/tool.my-plan.smarttwin.report-first.title'), false);
    }

    public function test_a_reset_of_the_resident_leaves_the_work_of_the_coach_alone(): void
    {
        $this->coachPlan();
        $this->openCallbacks();
        app(WoonplanLock::class)->lock($this->building);

        $this->reset([InputSource::resident()])->assertRedirect();

        $building = $this->building->fresh();
        $this->assertFalse($building->hasSmartTwinCallback(EventType::RESIDENT_SCAN_FINISHED));
        $this->assertTrue($building->hasSmartTwinCallback(EventType::COACH_SCAN_FINISHED));
        $this->assertNotNull($building->smartTwinPackage()->first());
        $this->assertTrue(app(WoonplanLock::class)->isLocked($building));
    }
}
