<?php

namespace Tests\Feature\app\Livewire\Cooperation\Frontend\Tool\SimpleScan\MyPlan;

use App\Enums\AdviceSource;
use App\Enums\MappingType;
use App\Helpers\HoomdossierSession;
use App\Helpers\RoleHelper;
use App\Livewire\Cooperation\Frontend\Tool\SimpleScan\MyPlan\Form;
use App\Models\Building;
use App\Models\Cooperation;
use App\Models\CustomMeasureApplication;
use App\Models\InputSource;
use App\Models\MeasureApplication;
use App\Models\Municipality;
use App\Models\Role;
use App\Models\Scan;
use App\Models\ToolQuestion;
use App\Models\User;
use App\Models\UserActionPlanAdvice;
use App\Services\MappingService;
use App\Services\ToolQuestionService;
use App\Services\Verbeterjehuis\RegulationService;
use App\Services\Verbeterjehuis\Verbeterjehuis;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * What a card in the woonplan says about regulations, as the resident reads it: a subsidy goes before
 * a loan, before and after the switch to SmartTwin.
 */
final class CardRegulationLabelTest extends TestCase
{
    use RefreshDatabase;

    public bool $seed = true;
    public string $seeder = DatabaseSeeder::class;

    private const SUBSIDY = 'cooperation/frontend/tool.my-plan.cards.regulations.subsidy-available';
    private const AFTER_SUBSIDY = 'cooperation/frontend/tool.my-plan.cards.regulations.after-subsidy-cut';
    private const LOAN = 'cooperation/frontend/tool.my-plan.cards.regulations.loan-available';

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

    private function card(?AdviceSource $source, bool $subsidy, bool $loan): void
    {
        $measure = MeasureApplication::findByShort('cavity-wall-insulation');

        // Without events: the observer would ask Verbeterjehuis for these flags.
        UserActionPlanAdvice::withoutEvents(fn () => UserActionPlanAdvice::withoutGlobalScopes()->forceCreate([
            'user_id'                         => $this->building->user_id,
            'input_source_id'                 => InputSource::master()->id,
            'user_action_plan_advisable_type' => MeasureApplication::class,
            'user_action_plan_advisable_id'   => $measure->id,
            'step_id'                         => $measure->step_id,
            'costs'                           => ['from' => null, 'to' => 1000],
            'category'                        => 'to-do',
            'visible'                         => true,
            'source'                          => $source?->value,
            'subsidy_available'               => $subsidy,
            'loan_available'                  => $loan,
        ]));
    }

    public static function regulations(): array
    {
        return [
            'subsidy and loan' => [true, true, self::SUBSIDY],
            'only a subsidy'   => [true, false, self::SUBSIDY],
            'only a loan'      => [false, true, self::LOAN],
            'neither'          => [false, false, null],
        ];
    }

    #[DataProvider('regulations')]
    public function test_after_the_switch_the_card_shows(bool $subsidy, bool $loan, ?string $label): void
    {
        $this->card(AdviceSource::SMART_TWIN, $subsidy, $loan);

        $this->assertLabel($label);
    }

    #[DataProvider('regulations')]
    public function test_before_the_switch_the_card_shows(bool $subsidy, bool $loan, ?string $label): void
    {
        $this->card(null, $subsidy, $loan);

        $this->assertLabel($label);
    }

    public function test_a_price_from_smarttwin_is_not_called_after_subsidy_whatever_the_user_once_entered(): void
    {
        $this->card(AdviceSource::SMART_TWIN, true, false);
        $this->userCosts();

        Livewire::test(Form::class, ['scan' => Scan::quick(), 'building' => $this->building])
            ->assertSee(__(self::SUBSIDY))
            ->assertDontSee(__(self::AFTER_SUBSIDY));
    }

    public function test_before_the_switch_a_price_the_user_entered_is_called_after_subsidy(): void
    {
        $this->card(null, true, false);
        $this->userCosts();

        Livewire::test(Form::class, ['scan' => Scan::quick(), 'building' => $this->building])
            ->assertSee(__(self::AFTER_SUBSIDY))
            ->assertDontSee(__(self::SUBSIDY));
    }

    public function test_an_own_measure_does_not_take_over_the_costs_of_the_card_before_it(): void
    {
        $this->card(null, false, false);
        $this->userCosts();

        $custom = CustomMeasureApplication::allInputSources()->forceCreate([
            'building_id'     => $this->building->id,
            'input_source_id' => InputSource::master()->id,
            'name'            => ['nl' => 'Eigen maatregel'],
            'info'            => ['nl' => 'Eigen maatregel'],
            'hash'            => (string) Str::uuid(),
        ]);
        UserActionPlanAdvice::withoutEvents(fn () => UserActionPlanAdvice::withoutGlobalScopes()->forceCreate([
            'user_id'                         => $this->building->user_id,
            'input_source_id'                 => InputSource::master()->id,
            'user_action_plan_advisable_type' => CustomMeasureApplication::class,
            'user_action_plan_advisable_id'   => $custom->id,
            'costs'                           => ['from' => null, 'to' => 500],
            'category'                        => 'to-do',
            'visible'                         => true,
            'order'                           => 1,
            'subsidy_available'               => true,
        ]));

        Livewire::test(Form::class, ['scan' => Scan::quick(), 'building' => $this->building])
            ->assertSee(__(self::SUBSIDY))
            ->assertDontSee(__(self::AFTER_SUBSIDY));
    }

    /**
     * What the resident once entered for the measure of the card, in the expert scan. Those questions
     * only count while they can be asked: the measure is considered, and Verbeterjehuis knows a
     * subsidy for it.
     */
    private function userCosts(): void
    {
        $municipality = Municipality::factory()->create();
        $this->building->update(['municipality_id' => $municipality->id]);
        MappingService::init()->from($municipality)
            ->sync([['Id' => '1', 'Name' => 'Testgemeente']], MappingType::MUNICIPALITY_VBJEHUIS->value);
        MappingService::init()->from(MeasureApplication::findByShort('cavity-wall-insulation'))->sync([['Value' => 'wall']]);
        $this->mock(Verbeterjehuis::class, fn (MockInterface $mock) => $mock->shouldReceive('regulation->search')->andReturn([
            ['Type' => RegulationService::SUBSIDY, 'TargetGroup' => 'Woningeigenaar', 'Tags' => [['Value' => 'wall']]],
        ]));

        $answers = [
            'wall-insulation-considerable'                    => 1,
            'has-cavity-wall'                                 => 1,
            'user-costs-cavity-wall-insulation-own-total'     => 2000,
            'user-costs-cavity-wall-insulation-subsidy-total' => 500,
        ];

        foreach ($answers as $short => $answer) {
            ToolQuestionService::init()
                ->toolQuestion(ToolQuestion::findByShort($short))
                ->building($this->building)
                ->currentInputSource(InputSource::resident())
                ->save($answer);
        }
    }

    private function assertLabel(?string $label): void
    {
        $component = Livewire::test(Form::class, ['scan' => Scan::quick(), 'building' => $this->building]);

        foreach ([self::SUBSIDY, self::LOAN] as $key) {
            $key === $label
                ? $component->assertSee(__($key))
                : $component->assertDontSee(__($key));
        }
    }
}
