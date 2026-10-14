<?php

namespace Tests\Feature\app\Livewire\Cooperation\Frontend\Tool\SimpleScan\MyPlan;

use App\Enums\AdviceSource;
use App\Helpers\HoomdossierSession;
use App\Helpers\RoleHelper;
use App\Livewire\Cooperation\Frontend\Tool\SimpleScan\MyPlan\Form;
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
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Moving a card in the woonplan: after the switch for resident, coach and master at once, whoever
 * moves it; before the switch for whoever moves it and master, as it always was.
 */
final class SharedWoonplanActionsTest extends TestCase
{
    use RefreshDatabase;

    public bool $seed = true;
    public string $seeder = DatabaseSeeder::class;

    private Building $building;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('hoomdossier.services.smarttwin.enabled', true);

        $cooperation = Cooperation::factory()->create();
        $cooperation->scans()->attach(Scan::findByShort(Scan::QUICK)->id);

        $resident = User::factory()->withAccount()->asResident()
            ->create(['cooperation_id' => $cooperation->id, 'allow_access' => true]);
        $this->building = Building::factory()->create(['user_id' => $resident->id]);

        $this->actingAs($resident->account);
        HoomdossierSession::setCooperation($cooperation);
        // The view builds its links from the cooperation of the request, which a component test
        // does not have.
        URL::defaults(['cooperation' => $cooperation->slug]);
        View::share('cooperation', $cooperation);
        HoomdossierSession::setHoomdossierSessions(
            $this->building,
            InputSource::resident(),
            InputSource::resident(),
            Role::findByName(RoleHelper::ROLE_RESIDENT),
        );
    }

    /**
     * The same card in all three input sources, as an import or a calculation leaves it.
     */
    private function card(?AdviceSource $source): UserActionPlanAdvice
    {
        $measure = MeasureApplication::findByShort('cavity-wall-insulation');

        // Without events: the trait would otherwise make a master row of its own next to ours.
        $rows = [];
        foreach ([InputSource::resident(), InputSource::coach(), InputSource::master()] as $inputSource) {
            $rows[$inputSource->short] = UserActionPlanAdvice::withoutEvents(fn () => UserActionPlanAdvice::withoutGlobalScopes()->forceCreate([
                'user_id'                         => $this->building->user_id,
                'input_source_id'                 => $inputSource->id,
                'user_action_plan_advisable_type' => MeasureApplication::class,
                'user_action_plan_advisable_id'   => $measure->id,
                'step_id'                         => $measure->step_id,
                'costs'                           => json_encode(['from' => null, 'to' => 1000]),
                'category'                        => 'to-do',
                'visible'                         => true,
                'source'                          => $source?->value,
            ]));
        }

        return $rows[InputSource::MASTER_SHORT];
    }

    /**
     * @return array<string, string> Category by input source short.
     */
    private function categories(): array
    {
        return UserActionPlanAdvice::withoutGlobalScopes()
            ->where('user_id', $this->building->user_id)
            ->get()
            ->mapWithKeys(fn (UserActionPlanAdvice $row) => [InputSource::find($row->input_source_id)->short => $row->category])
            ->sortKeys()
            ->all();
    }

    private function moveToLater(UserActionPlanAdvice $master): void
    {
        Livewire::test(Form::class, ['scan' => Scan::quick(), 'building' => $this->building])
            ->call('updateAdvice', $master->id, ['category' => 'later']);
    }

    public function test_after_the_switch_the_resident_moves_the_card_for_everyone(): void
    {
        $master = $this->card(AdviceSource::SMART_TWIN);

        $this->moveToLater($master);

        $this->assertSame(['coach' => 'later', 'master' => 'later', 'resident' => 'later'], $this->categories());
    }

    public function test_before_the_switch_the_coach_keeps_their_own_row(): void
    {
        $master = $this->card(null);

        $this->moveToLater($master);

        $this->assertSame(['coach' => 'to-do', 'master' => 'later', 'resident' => 'later'], $this->categories());
    }

    public function test_after_the_switch_the_verbeterjehuis_subsidy_is_not_shown_on_a_card(): void
    {
        $master = $this->card(AdviceSource::SMART_TWIN);
        UserActionPlanAdvice::withoutEvents(fn () => $master->update(['subsidy_available' => true]));

        $cards = Livewire::test(Form::class, ['scan' => Scan::quick(), 'building' => $this->building])->get('cards');

        $this->assertFalse(collect($cards)->flatten(1)->firstWhere('id', $master->id)['subsidy_available']);
    }

    public function test_before_the_switch_the_verbeterjehuis_subsidy_stays(): void
    {
        $master = $this->card(null);
        UserActionPlanAdvice::withoutEvents(fn () => $master->update(['subsidy_available' => true]));

        $cards = Livewire::test(Form::class, ['scan' => Scan::quick(), 'building' => $this->building])->get('cards');

        $this->assertTrue((bool) collect($cards)->flatten(1)->firstWhere('id', $master->id)['subsidy_available']);
    }
}
