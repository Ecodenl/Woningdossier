<?php

namespace Tests\Feature\app\Http\Controllers\Cooperation\Frontend\Tool\SimpleScan;

use App\Enums\SmartTwin\EventType;
use App\Helpers\HoomdossierSession;
use App\Helpers\RoleHelper;
use App\Models\Building;
use App\Models\Cooperation;
use App\Models\InputSource;
use App\Models\MeasureApplication;
use App\Models\Role;
use App\Models\Scan;
use App\Models\User;
use App\Models\UserActionPlanAdvice;
use App\Services\SmartTwin\Api\UserRole;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Exceptions;
use RuntimeException;
use Tests\TestCase;

/**
 * An empty woonplan means something different depending on the mode. Without SmartTwin there is
 * always an unanswered question to send the resident back to. With it, the questions are asked
 * elsewhere, so there are two other things it can mean: the check has not been done, or it has and
 * the advice is still coming.
 */
final class MyPlanControllerTest extends TestCase
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

        // These render a full page; the asset manifest is a build artefact CI does not produce.
        $this->withoutVite();

        $this->cooperation = Cooperation::factory()->create();
        $this->scan = Scan::findByShort(Scan::QUICK);
        $this->cooperation->scans()->attach($this->scan->id);

        $resident = User::factory()
            ->withAccount()
            ->asResident()
            ->create(['cooperation_id' => $this->cooperation->id, 'allow_access' => true]);

        $this->building = Building::factory()->create(['user_id' => $resident->id]);

        $inputSource = InputSource::findByShort(InputSource::RESIDENT_SHORT);

        $this->actingAs($resident->account);
        HoomdossierSession::setCooperation($this->cooperation);
        HoomdossierSession::setHoomdossierSessions(
            $this->building,
            $inputSource,
            $inputSource,
            Role::findByName(RoleHelper::ROLE_RESIDENT),
        );

        // The guard is skipped locally and in testing, which would hand every case the filled
        // woonplan and make these tests prove nothing.
        config()->set('hoomdossier.skip_woonplan_guard', false);
    }

    private function enableSmartTwin(bool $enabled): void
    {
        config()->set('hoomdossier.services.smarttwin.enabled', $enabled);
    }

    /**
     * Accounts get their SmartTwin id when they are created; the factory knows nothing about that.
     */
    private function linkSmartTwinAccount(): void
    {
        $account = $this->building->user->account;
        $account->linkSmartTwinUser('st-user-1', UserRole::Resident);

        // Hoomdossier::account() hands back the authenticated instance, which was handed to the
        // guard before the link and would still be showing no id.
        $this->actingAs($account->fresh());
    }

    private function visitWoonplan()
    {
        return $this->get(route('cooperation.frontend.tool.simple-scan.my-plan.index', [
            'cooperation' => $this->cooperation,
            'scan' => $this->scan,
        ]));
    }

    public function test_without_smart_twin_an_empty_woonplan_redirects_into_the_scan(): void
    {
        $this->enableSmartTwin(false);

        $response = $this->visitWoonplan();

        $response->assertRedirect();
        $this->assertStringContainsString(
            $this->scan->short,
            $response->headers->get('Location'),
            'Expected to be sent back into the scan.',
        );
    }

    public function test_with_smart_twin_an_empty_woonplan_invites_the_resident_to_do_the_check(): void
    {
        $this->enableSmartTwin(true);
        $this->linkSmartTwinAccount();

        $response = $this->visitWoonplan();

        $response->assertOk();
        $response->assertSee(__('cooperation/frontend/tool.my-plan.smarttwin.button'), false);
    }

    public function test_the_check_is_not_offered_without_a_smart_twin_account(): void
    {
        $this->enableSmartTwin(true);
        Exceptions::fake();

        $response = $this->visitWoonplan();

        $response->assertOk();
        // The factory account has no SmartTwin id, so there is nowhere to send them.
        $response->assertDontSee(__('cooperation/frontend/tool.my-plan.smarttwin.button'), false);
        $response->assertSee(__('cooperation/frontend/tool.my-plan.smarttwin.errors.not_configured'), false);

        // The page stays up, but this is not supposed to happen, so it gets reported.
        Exceptions::assertReported(RuntimeException::class);
    }

    public function test_the_check_is_not_offered_when_the_smart_twin_user_is_for_the_other_role(): void
    {
        $this->enableSmartTwin(true);
        Exceptions::fake();

        // An account gets one SmartTwin user and coach wins when it holds both roles, so someone
        // who coaches elsewhere carries an Advisor user into their own resident session. SmartTwin
        // refuses a quick-scan link for that, so the button would lead to an error.
        $account = $this->building->user->account;
        $account->linkSmartTwinUser('st-user-1', UserRole::Advisor);
        $this->actingAs($account->fresh());

        $response = $this->visitWoonplan();

        $response->assertOk();
        $response->assertDontSee(__('cooperation/frontend/tool.my-plan.smarttwin.button'), false);

        // A known trade-off of one user per account, not a fault of ours.
        Exceptions::assertNothingReported();
    }

    public function test_a_role_smart_twin_has_no_tool_for_is_not_reported(): void
    {
        $this->enableSmartTwin(true);
        Exceptions::fake();

        // A coordinator is skipped when SmartTwin users are created, so having no id is by design
        // rather than a fault. Reporting it would bury the case that is a fault in noise.
        // withBuilding() because every real user has one: registration and admin creation both go
        // through UserService::create(), which makes one. The factory does not, and
        // isFillingToolForOtherBuilding() compares against the user's own building.
        $coordinator = User::factory()
            ->withAccount()
            ->withBuilding()
            ->asCoordinator()
            ->create(['cooperation_id' => $this->cooperation->id]);

        $inputSource = InputSource::findByShort(InputSource::COOPERATION_SHORT);

        $this->actingAs($coordinator->account);
        HoomdossierSession::setHoomdossierSessions(
            $this->building,
            $inputSource,
            $inputSource,
            Role::findByName(RoleHelper::ROLE_COORDINATOR),
        );

        $this->visitWoonplan();

        Exceptions::assertNothingReported();
    }

    public function test_the_invitation_shows_even_when_the_scan_says_it_is_done(): void
    {
        $this->enableSmartTwin(true);
        $this->linkSmartTwinAccount();

        // The guard is skipped on local and accept, and a building that filled the scan in before
        // SmartTwin was switched on has its steps completed. Either way the scan reports "done"
        // while there is nothing to put on the board.
        config()->set('hoomdossier.skip_woonplan_guard', true);
        config()->set('app.env', 'accept');

        $response = $this->visitWoonplan();

        $response->assertOk();
        $response->assertSee(__('cooperation/frontend/tool.my-plan.smarttwin.button'), false);
        $response->assertDontSee(__('cooperation/frontend/tool.my-plan.categories.' . \App\Services\UserActionPlanAdviceService::CATEGORY_TO_DO), false);
    }

    public function test_the_woonplan_shows_as_soon_as_there_is_an_advice(): void
    {
        $this->enableSmartTwin(true);
        $this->linkSmartTwinAccount();

        UserActionPlanAdvice::withoutGlobalScopes()->create([
            'user_id' => $this->building->user->id,
            'input_source_id' => InputSource::findByShort(InputSource::MASTER_SHORT)->id,
            'user_action_plan_advisable_type' => MeasureApplication::class,
            'user_action_plan_advisable_id' => MeasureApplication::first()->id,
            'category' => \App\Services\UserActionPlanAdviceService::CATEGORY_TO_DO,
        ]);

        $response = $this->visitWoonplan();

        $response->assertOk();
        $response->assertDontSee(__('cooperation/frontend/tool.my-plan.start-check.body'), false);
    }

    public function test_the_demo_switch_holds_the_woonplan_on_its_invitation(): void
    {
        $this->enableSmartTwin(true);
        $this->linkSmartTwinAccount();

        UserActionPlanAdvice::withoutGlobalScopes()->create([
            'user_id' => $this->building->user->id,
            'input_source_id' => InputSource::findByShort(InputSource::MASTER_SHORT)->id,
            'user_action_plan_advisable_type' => MeasureApplication::class,
            'user_action_plan_advisable_id' => MeasureApplication::first()->id,
            'category' => \App\Services\UserActionPlanAdviceService::CATEGORY_TO_DO,
        ]);

        // Off, the advice above is enough to open the woonplan.
        $this->assertStringNotContainsString(
            __('cooperation/frontend/tool.my-plan.start-check.body'),
            $this->visitWoonplan()->getContent(),
        );

        config()->set('hoomdossier.demo_empty_woonplan', true);

        $response = $this->visitWoonplan();
        $response->assertOk();
        $response->assertSee(__('cooperation/frontend/tool.my-plan.smarttwin.button'), false);
    }

    public function test_with_an_advice_in_flight_the_resident_waits_instead(): void
    {
        $this->enableSmartTwin(true);

        $this->building->setSmartTwinCallback(EventType::RESIDENT_SCAN_FINISHED, ['dossierId' => 'abc']);
        $this->building->save();

        $response = $this->visitWoonplan();

        $response->assertOk();
        $response->assertSee(__('cooperation/frontend/tool.my-plan.awaiting-advice.body'), false);
        $response->assertDontSee(__('cooperation/frontend/tool.my-plan.smarttwin.button'), false);
    }
}
