<?php

namespace Tests\Feature\app\Http\Controllers\Cooperation\Frontend\Tool\SimpleScan;

use App\Enums\AdviceSource;
use App\Helpers\HoomdossierSession;
use App\Helpers\RoleHelper;
use App\Livewire\Cooperation\Frontend\Tool\SimpleScan\MyPlan\CalculationsTable;
use App\Models\Building;
use App\Models\Cooperation;
use App\Models\EnergyLabel;
use App\Models\InputSource;
use App\Models\MeasureApplication;
use App\Models\Role;
use App\Models\Scan;
use App\Models\SmartTwinPackage;
use App\Models\ToolQuestion;
use App\Models\User;
use App\Models\UserActionPlanAdvice;
use App\Services\ExecutedAdviceService;
use App\Services\SmartTwin\HouseData;
use App\Services\SmartTwin\WoonplanLock;
use App\Services\ToolQuestionService;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Testing\TestResponse;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * "Gegevens woning bekijken": the dwelling as it is now, from the dossier SmartTwin's import filled,
 * once the woonplan is SmartTwin's; and "Uitleg berekeningen" saying the calculation is SmartTwin's
 * from then on (decision 2026-10-15-smarttwin-gegevens-woning-pagina).
 */
final class HouseDataPageTest extends TestCase
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
        $this->cooperation->scans()->syncWithoutDetaching([$this->scan->id]);

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

    private function answer(string $short, mixed $value, ?InputSource $inputSource = null): void
    {
        ToolQuestionService::init()
            ->toolQuestion(ToolQuestion::findByShort($short))
            ->building($this->building)
            ->currentInputSource($inputSource ?? InputSource::coach())
            ->save($value);
    }

    /**
     * The woonplan SmartTwin's import leaves: a card for cavity wall insulation for everyone, and the
     * package with SmartTwin's calculation of the dwelling as it is.
     */
    private function switchToSmartTwin(): UserActionPlanAdvice
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
            'current_result'          => ['energyLabel' => 'C'],
            'scenario_result'         => ['energyLabel' => 'A+++'],
            'imported_at'             => Carbon::parse('2026-10-14 10:00'),
        ]);

        return $rows[InputSource::MASTER_SHORT];
    }

    private function page(): TestResponse
    {
        return $this->get(route('cooperation.frontend.tool.simple-scan.my-plan.house-data', ['cooperation' => $this->cooperation, 'scan' => $this->scan]));
    }

    private function woonplan(): TestResponse
    {
        return $this->get(route('cooperation.frontend.tool.simple-scan.my-plan.index', ['cooperation' => $this->cooperation, 'scan' => $this->scan]));
    }

    public function test_the_page_shows_the_dwelling_as_smart_twin_left_it(): void
    {
        $this->switchToSmartTwin();
        $this->answer('build-year', 1975);
        $this->answer('wall-surface', 80);
        $this->answer('energy-label', EnergyLabel::fromName('D')->id);

        $this->page()
            ->assertOk()
            ->assertSee(__('cooperation/frontend/tool.my-plan.house-data.title'))
            ->assertSee('1975')
            ->assertSee(__('cooperation/frontend/tool.my-plan.house-data.parts.facade'))
            ->assertSee('Geveloppervlakte van de woning')
            // Officially D; SmartTwin calculates C for the dwelling as it is. Not the label after the package.
            ->assertSee('icon-label-d', false)
            ->assertSee('icon-label-c', false)
            ->assertDontSee('A+++')
            ->assertSee(__('cooperation/frontend/tool.my-plan.house-data.according-to', ['date' => '14 oktober 2026']));
    }

    public function test_a_measure_carried_out_shows_at_its_part(): void
    {
        $advice = $this->switchToSmartTwin();
        $this->answer('wall-surface', 80);
        app(ExecutedAdviceService::class)->markExecuted($advice, Carbon::parse('2026-09-01'), 'Spouw gevuld door Isobouw');

        $data = HouseData::for($this->building);

        $this->assertSame('Spouw gevuld door Isobouw', $data->parts['facade']['executed'][0]['description']);
        $this->assertSame('2026-09-01', $data->parts['facade']['executed'][0]['date']->toDateString());
        $this->assertArrayNotHasKey('roof', $data->parts);
        $this->page()->assertSee('Spouw gevuld door Isobouw');
    }

    public function test_only_the_questions_smart_twin_fills_make_up_the_state(): void
    {
        $this->switchToSmartTwin();
        // Left from before SmartTwin, in a step it does not fill yet.
        $this->answer('ventilation-type', ToolQuestion::findByShort('ventilation-type')->getQuestionValues()->first()['value'], InputSource::resident());
        $this->answer('wall-surface', 80);

        $data = HouseData::for($this->building);

        $labels = collect($data->parts)->flatMap(fn (array $part) => array_column($part['rows'], 'label'))->all();
        $this->assertContains('Geveloppervlakte van de woning', $labels);
        $this->assertNotContains(ToolQuestion::findByShort('ventilation-type')->name, $labels);
    }

    public function test_usage_shows_what_the_dossier_holds(): void
    {
        $this->switchToSmartTwin();
        $this->assertSame([], HouseData::for($this->building)->usage);

        $this->answer('resident-count', 3, InputSource::resident());

        $usage = HouseData::for($this->building)->usage;
        $this->assertCount(1, $usage);
        // As the question offers it, like the dossier's summaries show it.
        $this->assertSame(['label' => 'Hoeveel mensen wonen er in de woning?', 'answer' => 'Drie'], $usage[0]);
    }

    public function test_the_woonplan_offers_the_page_once_it_is_smart_twins(): void
    {
        $this->switchToSmartTwin();

        $this->woonplan()->assertOk()->assertSee(__('cooperation/frontend/tool.my-plan.house-data.button'));
    }

    public function test_before_the_switch_there_is_no_page(): void
    {
        $this->page()->assertRedirect(route('cooperation.frontend.tool.simple-scan.my-plan.index', ['cooperation' => $this->cooperation, 'scan' => $this->scan]));
    }

    public function test_while_the_resident_waits_for_the_coach_there_is_no_page(): void
    {
        $this->switchToSmartTwin();
        // The coach started an advice and has not delivered it: the last plan is the resident's own.
        SmartTwinPackage::where('building_id', $this->building->id)->update(['input_source_id' => InputSource::resident()->id]);
        app(WoonplanLock::class)->lock($this->building);

        $this->page()->assertRedirect(route('cooperation.frontend.tool.simple-scan.my-plan.index', ['cooperation' => $this->cooperation, 'scan' => $this->scan]));
    }

    public function test_the_explanation_says_the_calculation_is_smart_twins_after_the_switch(): void
    {
        $this->switchToSmartTwin();

        Livewire::test(CalculationsTable::class, ['building' => $this->building])
            ->assertSee(strip_tags(__('cooperation/frontend/tool.my-plan.calculations.smart-twin')))
            ->assertDontSee('Kostenkentallen');
    }

    public function test_the_explanation_is_hoomdossiers_before_the_switch(): void
    {
        Livewire::test(CalculationsTable::class, ['building' => $this->building])
            ->assertSee('Kostenkentallen')
            ->assertDontSee(strip_tags(__('cooperation/frontend/tool.my-plan.calculations.smart-twin')));
    }
}
