<?php

namespace Tests\Feature\app\Livewire\Cooperation\Frontend\Tool\SimpleScan\MyPlan;

use App\Helpers\HoomdossierSession;
use App\Helpers\RoleHelper;
use App\Livewire\Cooperation\Frontend\Tool\SimpleScan\MyPlan\AwaitingAdvice;
use App\Models\Building;
use App\Models\Cooperation;
use App\Models\InputSource;
use App\Models\MeasureApplication;
use App\Models\Role;
use App\Models\Scan;
use App\Models\User;
use App\Models\UserActionPlanAdvice;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * The poll comes in on /livewire/update, outside the cooperation middleware, so nothing has set the
 * cooperation as a URL default. Deliberately not set here either: that is the situation in which
 * the redirect to the woonplan has to work.
 */
final class AwaitingAdviceTest extends TestCase
{
    use RefreshDatabase;

    public bool $seed = true;
    public string $seeder = DatabaseSeeder::class;

    private Cooperation $cooperation;
    private Building $building;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cooperation = Cooperation::factory()->create();
        $this->cooperation->scans()->attach(Scan::findByShort(Scan::QUICK)->id);

        $resident = User::factory()->withAccount()->asResident()
            ->create(['cooperation_id' => $this->cooperation->id, 'allow_access' => true]);
        $this->building = Building::factory()->create(['user_id' => $resident->id]);

        $this->actingAs($resident->account);
        HoomdossierSession::setCooperation($this->cooperation);
        HoomdossierSession::setHoomdossierSessions(
            $this->building,
            InputSource::resident(),
            InputSource::resident(),
            Role::findByName(RoleHelper::ROLE_RESIDENT),
        );
    }

    private function giveAdvice(): void
    {
        $measure = MeasureApplication::findByShort('cavity-wall-insulation');

        // Without events: the trait would otherwise make rows for the other input sources as well.
        UserActionPlanAdvice::withoutEvents(fn () => UserActionPlanAdvice::withoutGlobalScopes()->forceCreate([
            'user_id'                         => $this->building->user_id,
            'input_source_id'                 => InputSource::master()->id,
            'user_action_plan_advisable_type' => MeasureApplication::class,
            'user_action_plan_advisable_id'   => $measure->id,
            'step_id'                         => $measure->step_id,
            'costs'                           => ['from' => null, 'to' => 1000],
            'category'                        => 'to-do',
            'visible'                         => true,
        ]));
    }

    public function test_it_keeps_waiting_while_there_is_no_advice(): void
    {
        Livewire::test(AwaitingAdvice::class, ['building' => $this->building, 'scan' => Scan::quick()])
            ->call('checkForAdvice')
            ->assertNoRedirect();
    }

    public function test_it_moves_on_to_the_woonplan_of_the_cooperation_once_the_advice_is_in(): void
    {
        $component = Livewire::test(AwaitingAdvice::class, ['building' => $this->building, 'scan' => Scan::quick()]);

        // The advice arrives on the webhook while the page is open.
        $this->giveAdvice();

        $component->call('checkForAdvice')
            ->assertRedirect(route('cooperation.frontend.tool.simple-scan.my-plan.index', [
                'cooperation' => $this->cooperation,
                'scan'        => Scan::quick(),
            ]));
    }
}
