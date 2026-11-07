<?php

namespace Tests\Feature\app\Http\Middleware;

use App\Enums\AdviceSource;
use App\Helpers\HoomdossierSession;
use App\Helpers\RoleHelper;
use App\Models\Building;
use App\Models\Cooperation;
use App\Models\InputSource;
use App\Models\MeasureApplication;
use App\Models\Role;
use App\Models\Scan;
use App\Models\Step;
use App\Models\User;
use App\Models\UserActionPlanAdvice;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * The expert scan closes when the woonplan goes over to SmartTwin, not when the cooperation takes
 * SmartTwin on: a finished dossier that never goes through SmartTwin keeps it.
 */
final class EnsureExpertScanAvailableTest extends TestCase
{
    use RefreshDatabase;

    public bool $seed = true;
    public string $seeder = DatabaseSeeder::class;

    private Cooperation $cooperation;
    private Building $building;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        config()->set('hoomdossier.services.smarttwin.enabled', true);

        $this->cooperation = Cooperation::factory()->create();
        $this->cooperation->scans()->attach([Scan::findByShort(Scan::QUICK)->id, Scan::findByShort(Scan::EXPERT)->id]);

        $resident = User::factory()->withAccount()->asResident()
            ->create(['cooperation_id' => $this->cooperation->id, 'allow_access' => true]);
        $this->building = Building::factory()->create(['user_id' => $resident->id]);

        $inputSource = InputSource::findByShort(InputSource::RESIDENT_SHORT);
        $this->actingAs($resident->account);
        HoomdossierSession::setCooperation($this->cooperation);
        HoomdossierSession::setHoomdossierSessions($this->building, $inputSource, $inputSource, Role::findByName(RoleHelper::ROLE_RESIDENT));
    }

    private function advice(?AdviceSource $source): void
    {
        $measure = MeasureApplication::findByShort('cavity-wall-insulation');

        UserActionPlanAdvice::withoutGlobalScopes()->create([
            'user_id'                         => $this->building->user_id,
            'input_source_id'                 => InputSource::findByShort(InputSource::RESIDENT_SHORT)->id,
            'user_action_plan_advisable_type' => MeasureApplication::class,
            'user_action_plan_advisable_id'   => $measure->id,
            'step_id'                         => $measure->step_id,
            'costs'                           => ['from' => null, 'to' => 1000],
            'source'                          => $source,
        ]);
    }

    private function myPlan(): string
    {
        return route('cooperation.frontend.tool.simple-scan.my-plan.index', [
            'cooperation' => $this->cooperation,
            'scan'        => Scan::quick(),
        ]);
    }

    private function expertStep(): string
    {
        return route('cooperation.frontend.tool.expert-scan.index', [
            'cooperation' => $this->cooperation,
            'step'        => Step::findByShort('wall-insulation'),
        ]);
    }

    public function test_after_the_switch_the_expert_scan_sends_you_to_the_woonplan(): void
    {
        $this->advice(AdviceSource::SMART_TWIN);

        $this->get($this->expertStep())->assertRedirect($this->myPlan());
    }

    public function test_after_the_switch_the_old_tool_pages_are_closed_too(): void
    {
        $this->advice(AdviceSource::SMART_TWIN);

        $this->get(route('cooperation.tool.wall-insulation.index', ['cooperation' => $this->cooperation]))
            ->assertRedirect($this->myPlan());
    }

    public function test_before_the_switch_a_finished_dossier_keeps_the_expert_scan(): void
    {
        // A woonplan from before SmartTwin; the cooperation takes part.
        $this->advice(null);

        // The facade step of the expert scan lives on the old tool page, as it always did.
        $toolPage = route('cooperation.tool.wall-insulation.index', ['cooperation' => $this->cooperation]);
        $this->get($this->expertStep())->assertRedirect($toolPage);

        $this->assertNotSame($this->myPlan(), $this->get($toolPage)->headers->get('Location'));
    }
}
