<?php

namespace Tests\Feature\app\Http\Controllers\Cooperation\Frontend\Tool\SimpleScan;

use App\Enums\AdviceSource;
use App\Helpers\HoomdossierSession;
use App\Helpers\RoleHelper;
use App\Models\Building;
use App\Models\Cooperation;
use App\Models\InputSource;
use App\Models\MeasureApplication;
use App\Models\Role;
use App\Models\Scan;
use App\Models\SmartTwinPackage;
use App\Models\User;
use App\Models\UserActionPlanAdvice;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

/**
 * The notice above the quick scan telling a coach what to do with the woonplan the resident made with
 * SmartTwin's quick scan: look at the dwelling first, then adjust the woonplan in SmartTwin.
 */
final class SmartTwinCoachNoticeTest extends TestCase
{
    use RefreshDatabase;

    public bool $seed = true;
    public string $seeder = DatabaseSeeder::class;

    private Cooperation $cooperation;
    private Building $building;
    private Scan $scan;
    private User $coach;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        config()->set('hoomdossier.services.smarttwin.enabled', true);
        config()->set('hoomdossier.skip_woonplan_guard', false);

        $this->cooperation = Cooperation::factory()->create();
        $this->scan = Scan::findByShort(Scan::QUICK);
        $this->cooperation->scans()->syncWithoutDetaching([$this->scan->id]);

        $resident = User::factory()->withAccount()->asResident()
            ->create(['cooperation_id' => $this->cooperation->id, 'allow_access' => true]);
        $this->building = Building::factory()->create(['user_id' => $resident->id]);

        $this->coach = User::factory()->withAccount()->asCoach()
            ->create(['cooperation_id' => $this->cooperation->id]);
        Building::factory()->create(['user_id' => $this->coach->id]);

        HoomdossierSession::setCooperation($this->cooperation);
        URL::defaults(['cooperation' => $this->cooperation->slug]);
        View::share('cooperation', $this->cooperation);
    }

    private function asCoach(): void
    {
        $this->actingAs($this->coach->account);
        HoomdossierSession::setHoomdossierSessions(
            $this->building,
            InputSource::coach(),
            InputSource::coach(),
            Role::findByName(RoleHelper::ROLE_COACH),
        );
    }

    private function asResident(): void
    {
        $this->actingAs($this->building->user->account);
        HoomdossierSession::setHoomdossierSessions(
            $this->building,
            InputSource::resident(),
            InputSource::resident(),
            Role::findByName(RoleHelper::ROLE_RESIDENT),
        );
    }

    /**
     * A SmartTwin woonplan: a card for everyone, and the package from the flow that brought it in.
     */
    private function smartTwinPlanFrom(InputSource $flow): void
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
            'input_source_id'         => $flow->id,
            'measure_application_ids' => [$measure->id],
            'imported_at'             => now(),
        ]);
    }

    private function woonplan(): TestResponse
    {
        return $this->get(route('cooperation.frontend.tool.simple-scan.my-plan.index', ['cooperation' => $this->cooperation, 'scan' => $this->scan]));
    }

    private function houseData(): TestResponse
    {
        return $this->get(route('cooperation.frontend.tool.simple-scan.my-plan.house-data', ['cooperation' => $this->cooperation, 'scan' => $this->scan]));
    }

    public function test_a_coach_is_told_what_to_do_with_the_residents_quick_scan(): void
    {
        $this->smartTwinPlanFrom(InputSource::resident());
        $this->asCoach();

        $houseDataUrl = route('cooperation.frontend.tool.simple-scan.my-plan.house-data', ['cooperation' => $this->cooperation, 'scan' => $this->scan]);

        // On every page of the quick scan, not only the woonplan.
        foreach ([$this->woonplan(), $this->houseData()] as $page) {
            $page->assertOk()
                // @lang does not escape, so neither are the quotes in the text.
                ->assertSee(__('cooperation/frontend/tool.my-plan.coach-notice.text'), false)
                ->assertSee(__('cooperation/frontend/tool.my-plan.coach-notice.warning'))
                ->assertSee('href="' . $houseDataUrl . '"', false);
        }
    }

    public function test_not_once_the_coach_advised(): void
    {
        $this->smartTwinPlanFrom(InputSource::coach());
        $this->asCoach();

        $this->woonplan()
            ->assertOk()
            ->assertDontSee(__('cooperation/frontend/tool.my-plan.coach-notice.text'), false);
    }

    public function test_not_for_the_resident(): void
    {
        $this->smartTwinPlanFrom(InputSource::resident());
        $this->asResident();

        $this->woonplan()
            ->assertOk()
            ->assertDontSee(__('cooperation/frontend/tool.my-plan.coach-notice.text'), false);
    }

    public function test_a_smart_twin_woonplan_does_not_point_to_the_closed_expert_scan(): void
    {
        $this->smartTwinPlanFrom(InputSource::resident());
        $this->asResident();

        $this->woonplan()
            ->assertOk()
            ->assertDontSee('verdiepingsvragen');
    }
}
