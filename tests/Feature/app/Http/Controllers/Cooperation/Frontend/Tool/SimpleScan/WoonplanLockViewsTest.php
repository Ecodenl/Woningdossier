<?php

namespace Tests\Feature\app\Http\Controllers\Cooperation\Frontend\Tool\SimpleScan;

use App\Enums\AdviceSource;
use App\Enums\SmartTwin\WoonplanState;
use App\Helpers\HoomdossierSession;
use App\Helpers\RoleHelper;
use App\Livewire\Cooperation\Frontend\Tool\SimpleScan\MyPlan\Form;
use App\Models\Building;
use App\Models\Cooperation;
use App\Models\InputSource;
use App\Models\MeasureApplication;
use App\Models\Role;
use App\Models\Scan;
use App\Models\SmartTwinPackage;
use App\Models\User;
use App\Models\UserActionPlanAdvice;
use App\Services\SmartTwin\WoonplanLock;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * The resident's woonplan while a coach works on it in SmartTwin, with the texts of the woonplan
 * design: "Nog even geduld" before the coach delivers a plan, the plan locked while the coach is not
 * done, and the plan open again once the coach is.
 */
final class WoonplanLockViewsTest extends TestCase
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

        $this->cooperation = Cooperation::factory()->create();
        $this->scan = Scan::findByShort(Scan::QUICK);
        $this->cooperation->scans()->attach($this->scan->id);

        $resident = User::factory()->withAccount()->asResident()
            ->create(['cooperation_id' => $this->cooperation->id, 'allow_access' => true]);
        $this->building = Building::factory()->create(['user_id' => $resident->id]);

        $this->actingAs($resident->account);
        HoomdossierSession::setCooperation($this->cooperation);
        URL::defaults(['cooperation' => $this->cooperation->slug]);
        View::share('cooperation', $this->cooperation);
        HoomdossierSession::setHoomdossierSessions(
            $this->building,
            InputSource::resident(),
            InputSource::resident(),
            Role::findByName(RoleHelper::ROLE_RESIDENT),
        );
    }

    /**
     * The plan the coach delivered: a SmartTwin card for everyone, and a package from the coach's flow.
     */
    private function coachPlan(): UserActionPlanAdvice
    {
        $measure = MeasureApplication::findByShort('cavity-wall-insulation');

        $rows = [];
        foreach ([InputSource::resident(), InputSource::coach(), InputSource::master()] as $inputSource) {
            $rows[$inputSource->short] = UserActionPlanAdvice::withoutEvents(fn () => UserActionPlanAdvice::withoutGlobalScopes()->forceCreate([
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

        return $rows[InputSource::MASTER_SHORT];
    }

    private function woonplanPage(): \Illuminate\Testing\TestResponse
    {
        return $this->get(route('cooperation.frontend.tool.simple-scan.my-plan.index', [
            'cooperation' => $this->cooperation,
            'scan'        => $this->scan,
        ]));
    }

    public function test_before_the_coach_delivers_the_resident_is_asked_for_patience(): void
    {
        app(WoonplanLock::class)->lock($this->building);

        $this->woonplanPage()
            ->assertOk()
            ->assertSee(__('cooperation/frontend/tool.my-plan.lock.awaiting.title'))
            ->assertDontSee(__('cooperation/frontend/tool.my-plan.lock.locked.text'));
    }

    public function test_the_resident_waiting_for_the_coach_can_ask_the_coach(): void
    {
        app(WoonplanLock::class)->lock($this->building);

        $this->woonplanPage()
            ->assertOk()
            ->assertSee('href="' . route('cooperation.my-account.messages.edit', ['cooperation' => $this->cooperation]) . '"', false);
    }

    public function test_while_the_coach_is_not_done_the_plan_is_shown_locked(): void
    {
        $this->coachPlan();
        app(WoonplanLock::class)->lock($this->building);

        $this->woonplanPage()
            ->assertOk()
            ->assertSee(__('cooperation/frontend/tool.my-plan.lock.locked.text'))
            ->assertDontSee(__('cooperation/frontend/tool.my-plan.title.quick-scan'))
            ->assertDontSee('verdiepingsvragen');
    }

    public function test_once_the_coach_is_done_the_resident_may_change_it(): void
    {
        $this->coachPlan();

        $this->woonplanPage()
            ->assertOk()
            ->assertSee(__('cooperation/frontend/tool.my-plan.lock.done.text'))
            ->assertDontSee(__('cooperation/frontend/tool.my-plan.title.quick-scan'))
            ->assertDontSee('verdiepingsvragen');
    }

    public function test_a_locked_woonplan_takes_no_changes_from_the_resident(): void
    {
        $card = $this->coachPlan();
        app(WoonplanLock::class)->lock($this->building);

        $woonplan = fn () => Livewire::test(Form::class, ['scan' => $this->scan, 'building' => $this->building]);
        $woonplan()->assertDontSeeHtml('wire:key="add-to-do"')
            ->assertDontSee(__('cooperation/frontend/tool.my-plan.package-results.adjust'));

        // Refused by the component itself, not only left out of the page.
        $woonplan()->call('cardMoved', 'to-do', 'to-do', (string) $card->id, '0')->assertForbidden();
        $woonplan()->call('openExecuted', $card->id)->assertForbidden();
        $woonplan()->call('updateAdvice', $card->id, ['category' => 'later'])->assertForbidden();

        $this->assertSame('to-do', $card->fresh()->category);
    }

    public function test_the_lock_is_the_residents_only(): void
    {
        $this->coachPlan();
        app(WoonplanLock::class)->lock($this->building);
        $lock = app(WoonplanLock::class);

        $this->assertSame(WoonplanState::LOCKED, $lock->stateFor($this->building, InputSource::resident(), false));
        $this->assertSame(WoonplanState::OPEN, $lock->stateFor($this->building, InputSource::coach(), false));
        // Someone observing changes nothing anyway, and sees the plan as it is.
        $this->assertSame(WoonplanState::OPEN, $lock->stateFor($this->building, InputSource::resident(), true));
    }
}
