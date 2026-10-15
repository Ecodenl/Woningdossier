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
use App\Models\SmartTwinPackage;
use App\Models\Step;
use App\Models\User;
use App\Models\UserActionPlanAdvice;
use App\Services\ExecutedAdviceService;
use App\Services\SmartTwin\SmartTwinAdviceDetails;
use Illuminate\Support\Carbon;
use Database\Seeders\DatabaseSeeder;
use App\Jobs\RefreshRegulationsForUserActionPlanAdvice;
use App\Models\CustomMeasureApplication;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * The card rules of a SmartTwin woonplan, enforced by the woonplan itself: a package card stays in
 * "Nu aanpakken" but may go in the trash, an own or small measure moves freely, and into "In orde"
 * asks for the "uitgevoerd" popup instead of moving.
 */
final class SmartTwinCardRulesTest extends TestCase
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
     * A card in all three input sources, as an import or a recalculation after the switch leaves it.
     */
    private function card(string $measureShort, ?AdviceSource $source, ?string $stepShort = null): UserActionPlanAdvice
    {
        $measure = MeasureApplication::findByShort($measureShort);

        $rows = [];
        foreach ([InputSource::resident(), InputSource::coach(), InputSource::master()] as $inputSource) {
            $rows[$inputSource->short] = UserActionPlanAdvice::withoutEvents(fn () => UserActionPlanAdvice::withoutGlobalScopes()->forceCreate([
                'user_id'                         => $this->building->user_id,
                'input_source_id'                 => $inputSource->id,
                'user_action_plan_advisable_type' => MeasureApplication::class,
                'user_action_plan_advisable_id'   => $measure->id,
                'step_id'                         => is_null($stepShort) ? $measure->step_id : Step::findByShort($stepShort)->id,
                'costs'                           => ['from' => null, 'to' => 1000],
                'category'                        => 'to-do',
                'visible'                         => true,
                'source'                          => $source?->value,
            ]));
        }

        return $rows[InputSource::MASTER_SHORT];
    }

    private function packageCard(): UserActionPlanAdvice
    {
        $card = $this->card('cavity-wall-insulation', AdviceSource::SMART_TWIN);

        SmartTwinPackage::create([
            'building_id'             => $this->building->id,
            'measure_application_ids' => [$card->user_action_plan_advisable_id],
            'imported_at'             => now(),
        ]);

        return $card;
    }

    private function woonplan(): Testable
    {
        return Livewire::test(Form::class, ['scan' => Scan::quick(), 'building' => $this->building]);
    }

    /**
     * @return array<int, string> The category of every row of the card's measure.
     */
    private function categoriesOf(UserActionPlanAdvice $card): array
    {
        return UserActionPlanAdvice::withoutGlobalScopes()
            ->where('user_id', $this->building->user_id)
            ->where('user_action_plan_advisable_id', $card->user_action_plan_advisable_id)
            ->pluck('category')
            ->unique()
            ->values()
            ->all();
    }

    private function cardData(Testable $woonplan, UserActionPlanAdvice $card): array
    {
        return collect($woonplan->get('cards'))->flatten(1)->firstWhere('id', $card->id);
    }

    public function test_a_package_card_does_not_move_to_later(): void
    {
        $card = $this->packageCard();

        $this->woonplan()->call('cardMoved', 'to-do', 'later', (string) $card->id, '0');

        $this->assertSame(['to-do'], $this->categoriesOf($card));
    }

    public function test_a_package_card_into_in_order_asks_for_the_uitgevoerd_popup(): void
    {
        $card = $this->packageCard();

        $this->woonplan()
            ->call('cardMoved', 'to-do', 'complete', (string) $card->id, '0')
            ->assertSet('executedAdviceId', $card->id)
            ->assertDispatched('open-executed-modal');

        // Not moved: it moves once the popup is saved.
        $this->assertSame(['to-do'], $this->categoriesOf($card));
    }

    public function test_a_package_card_goes_in_the_trash_for_everyone_and_breaks_the_package(): void
    {
        $card = $this->packageWithFigures();

        $this->woonplan()
            ->call('cardTrashed', 'to-do', (string) $card->id)
            ->assertSee(__('cooperation/frontend/tool.my-plan.package-results.broken'))
            ->assertDontSee('€ 2.520');

        $this->assertSame([0], UserActionPlanAdvice::withoutGlobalScopes()
            ->where('user_id', $this->building->user_id)
            ->where('user_action_plan_advisable_id', $card->user_action_plan_advisable_id)
            ->pluck('visible')
            ->map(fn ($visible) => (int) $visible)
            ->unique()
            ->values()
            ->all());
    }

    public function test_an_in_order_card_does_not_go_in_the_trash(): void
    {
        $this->packageCard();
        $card = $this->card('floor-insulation', AdviceSource::SMART_TWIN);
        UserActionPlanAdvice::withoutGlobalScopes()
            ->where('user_id', $this->building->user_id)
            ->where('user_action_plan_advisable_id', $card->user_action_plan_advisable_id)
            ->update(['category' => 'complete', 'costs' => ['from' => null, 'to' => null]]);

        $this->woonplan()->call('cardTrashed', 'complete', (string) $card->id);

        $this->assertTrue((bool) $card->fresh()->visible);
    }

    public function test_a_small_measure_moves_freely_for_everyone(): void
    {
        $this->packageCard();
        $small = $this->card('save-energy-with-light', null, 'small-measures');

        $this->woonplan()->call('cardMoved', 'to-do', 'later', (string) $small->id, '0');

        $this->assertSame(['later'], $this->categoriesOf($small));
    }

    public function test_the_cards_tell_the_browser_what_they_may_do(): void
    {
        $package = $this->packageCard();
        $small = $this->card('save-energy-with-light', null, 'small-measures');

        $woonplan = $this->woonplan();
        $packageCard = $this->cardData($woonplan, $package);
        $smallCard = $this->cardData($woonplan, $small);

        $this->assertTrue($packageCard['in_package']);
        $this->assertSame(['complete', 'to-do'], $packageCard['targets']);
        $this->assertTrue($packageCard['trashable']);

        $this->assertFalse($smallCard['in_package']);
        $this->assertSame(['complete', 'to-do', 'later'], $smallCard['targets']);
        $this->assertTrue($smallCard['trashable']);

        $woonplan->assertSee(__('cooperation/frontend/tool.my-plan.cards.package-label'));
        // A "+" under "Nu aanpakken" and one under "Later uitvoeren".
        $woonplan->assertSeeHtml('wire:key="add-to-do"')->assertSeeHtml('wire:key="add-later"');
        // And the "Uitgevoerd" button on a card.
        $woonplan->assertSeeHtml('wire:key="executed-' . $package->id . '"');
    }

    public function test_before_the_switch_every_card_moves_as_it_always_did(): void
    {
        // A calculated woonplan in a cooperation that took SmartTwin on, without a SmartTwin advice.
        $card = $this->card('cavity-wall-insulation', null);

        $woonplan = $this->woonplan();
        $data = $this->cardData($woonplan, $card);
        $this->assertNull($data['kind']);
        $this->assertTrue($data['trashable']);
        $woonplan->assertDontSee(__('cooperation/frontend/tool.my-plan.cards.package-label'));
        $woonplan->assertDontSeeHtml('wire:key="add-later"');

        $woonplan->call('cardMoved', 'to-do', 'complete', (string) $card->id, '0');
        $this->assertSame('complete', $card->fresh()->category);
    }

    /**
     * Adds an own measure through the form behind the "+" of a column.
     */
    private function addOwnMeasure(string $column, string $name = 'Zonnescherm'): void
    {
        // Saving asks Verbeterjehuis for regulations; not what is under test.
        Bus::fake([RefreshRegulationsForUserActionPlanAdvice::class]);

        $woonplan = $this->woonplan();
        $index = array_key_last($woonplan->get('customMeasureApplicationsFormData'));

        $woonplan->set('newMeasureCategory', $column)
            ->set("customMeasureApplicationsFormData.{$index}.name", $name)
            ->set("customMeasureApplicationsFormData.{$index}.info", 'Eigen idee')
            ->set("customMeasureApplicationsFormData.{$index}.costs.from", '1200')
            ->call('save', $index)
            ->assertHasNoErrors();
    }

    /**
     * @return array<int, string> The category of every row of the own measure's advice.
     */
    private function ownMeasureCategories(): array
    {
        return UserActionPlanAdvice::withoutGlobalScopes()
            ->where('user_id', $this->building->user_id)
            ->where('user_action_plan_advisable_type', CustomMeasureApplication::class)
            ->pluck('category')
            ->unique()
            ->values()
            ->all();
    }

    public function test_the_plus_of_later_adds_an_own_measure_there_for_everyone(): void
    {
        $this->packageCard();

        $this->addOwnMeasure('later');

        $this->assertSame(['later'], $this->ownMeasureCategories());
        $this->assertSame(3, UserActionPlanAdvice::withoutGlobalScopes()
            ->where('user_id', $this->building->user_id)
            ->where('user_action_plan_advisable_type', CustomMeasureApplication::class)
            ->count());
    }

    public function test_an_own_measure_only_ever_lands_in_now_or_later(): void
    {
        $this->packageCard();

        // Not a column the "+" offers.
        $this->addOwnMeasure('complete');

        $this->assertSame(['to-do'], $this->ownMeasureCategories());
    }

    public function test_editing_an_own_measure_leaves_it_in_its_column(): void
    {
        $this->packageCard();
        $this->addOwnMeasure('later');

        $woonplan = $this->woonplan();
        $index = collect($woonplan->get('customMeasureApplicationsFormData'))->search(fn ($data) => 'Zonnescherm' === $data['name']);
        $woonplan->set("customMeasureApplicationsFormData.{$index}.name", 'Zonnescherm (elektrisch)')
            ->call('save', $index)
            ->assertHasNoErrors();

        $this->assertSame(['later'], $this->ownMeasureCategories());
    }

    public function test_before_the_switch_a_new_own_measure_goes_to_now_as_it_always_did(): void
    {
        $this->addOwnMeasure('later');

        $this->assertSame(['to-do'], $this->ownMeasureCategories());
    }

    private function packageWithFigures(): UserActionPlanAdvice
    {
        $card = $this->packageCard();

        // The figures of dossier 100271.
        $this->building->smartTwinPackage()->first()->update([
            'current_result'                => ['energyConsumptionGas' => 1500, 'energyConsumptionElectricity' => 2500, 'totalCo2Emission' => 5826.2365, 'energyLabel' => 'C'],
            'scenario_result'               => ['energyConsumptionGas' => 0, 'energyConsumptionElectricity' => 6035.9627, 'totalCo2Emission' => 1162.0925, 'energyLabel' => 'A+++'],
            'energy_cost_savings_per_month' => 210,
            'total_subsidy'                 => 6853.749263548063,
            'subsidies'                     => [['name' => 'ISDE', 'amount' => 6853.749263548063]],
        ]);

        return $card;
    }

    public function test_the_results_show_the_package_its_own_measures_and_its_figures(): void
    {
        $this->packageWithFigures();
        $this->addOwnMeasure('to-do', 'Zonnescherm');

        $this->woonplan()
            ->assertSee(__('cooperation/frontend/tool.my-plan.package-results.title'))
            // A line per card in "Nu aanpakken", the own measure by its name.
            ->assertSeeInOrder([MeasureApplication::findByShort('cavity-wall-insulation')->measure_name, 'Zonnescherm'])
            ->assertSee('€ 2.200')
            ->assertSee('€ 2.520')
            // As SmartTwin gave it, not rounded to the woonplan's steps of five.
            // Whole euros, not the woonplan's steps of five.
            ->assertSee('€ 6.854')
            ->assertSee('ISDE')
            ->assertSee('C → A+++');
    }

    public function test_once_the_package_no_longer_holds_its_figures_make_way_for_a_new_calculation(): void
    {
        $card = $this->packageWithFigures();
        app(ExecutedAdviceService::class)->markExecuted($card, Carbon::parse('2026-10-01'));

        $this->woonplan()
            ->assertSee(__('cooperation/frontend/tool.my-plan.package-results.broken'))
            ->assertDontSee('€ 2.520');
    }

    public function test_before_the_switch_there_are_no_package_results(): void
    {
        $this->card('cavity-wall-insulation', null);

        $this->woonplan()->assertDontSee(__('cooperation/frontend/tool.my-plan.package-results.title'));
    }

    public function test_the_popup_marks_the_measure_carried_out_with_its_documents(): void
    {
        Storage::fake('uploads');
        $card = $this->packageWithFigures();

        $this->woonplan()
            ->call('openExecuted', $card->id)
            ->set('executedAt', '2026-10-01')
            ->set('executedDescription', 'Spouwmuur gevuld door Isoleer BV')
            ->set('executedDocuments', [UploadedFile::fake()->create('factuur-isoleer-bv.pdf', 20, 'application/pdf')])
            ->call('saveExecuted')
            ->assertHasNoErrors()
            // In "In orde", and the package no longer holds.
            ->assertSee(__('cooperation/frontend/tool.my-plan.executed.executed-on', ['date' => '01-10-2026']))
            ->assertSee(__('cooperation/frontend/tool.my-plan.package-results.broken'));

        $this->assertSame(['complete'], $this->categoriesOf($card));
        $this->assertSame('Spouwmuur gevuld door Isoleer BV', $card->fresh()->executed_description);
        $document = $this->building->media()->first();
        $this->assertSame('factuur-isoleer-bv', $document->filename);
        $this->assertSame('invoice', $document->pivot->tag);
    }

    public function test_the_cards_reload_without_the_cooperation_as_url_default(): void
    {
        $card = $this->packageCard();
        $woonplan = $this->woonplan();
        $this->assertNotNull($this->cardData($woonplan, $card)['route'], 'a card that links to the expert scan');

        // A Livewire update request does not pass the cooperation middleware, which sets this default.
        URL::defaults(['cooperation' => null]);

        $woonplan->call('openExecuted', $card->id)
            ->set('executedAt', '2026-10-01')
            ->call('saveExecuted')
            ->assertHasNoErrors();

        $this->assertSame(['complete'], $this->categoriesOf($card));
        $this->assertStringContainsString(
            $this->building->user->cooperation->slug,
            $this->cardData($woonplan, $card)['route'],
        );
    }

    public function test_a_measure_cannot_be_carried_out_in_the_future(): void
    {
        $card = $this->packageCard();

        $this->woonplan()
            ->call('openExecuted', $card->id)
            ->set('executedAt', now()->addDay()->format('Y-m-d'))
            ->call('saveExecuted')
            ->assertHasErrors(['executedAt']);

        $this->assertSame(['to-do'], $this->categoriesOf($card));
    }

    public function test_the_popup_takes_it_back(): void
    {
        $card = $this->packageCard();
        app(ExecutedAdviceService::class)->markExecuted($card, Carbon::parse('2026-10-01'));

        $this->woonplan()
            ->call('openExecuted', $card->id)
            ->assertSet('executedAlready', true)
            ->assertSet('executedAt', '2026-10-01')
            ->call('undoExecuted');

        $this->assertSame(['to-do'], $this->categoriesOf($card));
        $this->assertNull($card->fresh()->executed_at);
    }

    public function test_a_carried_out_measure_advised_again_is_marked(): void
    {
        $card = $this->packageCard();
        // What an import leaves when SmartTwin puts a carried-out measure back in the package.
        UserActionPlanAdvice::withoutGlobalScopes()
            ->where('user_action_plan_advisable_id', $card->user_action_plan_advisable_id)
            ->update(['executed_at' => '2026-10-01']);

        $this->woonplan()->assertSee(__('cooperation/frontend/tool.my-plan.executed.advised-again', ['date' => '01-10-2026']));
    }

    public function test_before_the_switch_there_is_no_popup(): void
    {
        $card = $this->card('cavity-wall-insulation', null);

        $this->woonplan()
            ->assertDontSee(__('cooperation/frontend/tool.my-plan.executed.button'))
            ->call('openExecuted', $card->id)
            ->assertForbidden();
    }

    public function test_a_smart_twin_amount_is_shown_in_whole_euros_alike_on_card_and_line(): void
    {
        $card = $this->packageWithFigures();
        UserActionPlanAdvice::withoutGlobalScopes()
            ->where('user_action_plan_advisable_id', $card->user_action_plan_advisable_id)
            ->update(['costs' => json_encode(['from' => null, 'to' => 1622.65])]);

        $woonplan = $this->woonplan();
        $html = $woonplan->html();
        $results = substr($html, strpos($html, 'id="package-results"'));
        $cards = substr($html, 0, strpos($html, 'id="package-results"'));

        // Not the woonplan's steps of five (€ 1.625), but whole euros, and the same on the card as
        // in its line (and the total, there being one line).
        $this->assertStringContainsString('€ 1.623', $cards);
        $this->assertSame(2, substr_count($results, '€ 1.623'), 'the line and the total');
        $this->assertStringNotContainsString('€ 1.625', $html);
    }

    public function test_the_i_of_a_package_card_tells_what_smart_twin_says(): void
    {
        $card = $this->packageCard();
        $details = new SmartTwinAdviceDetails(
            [SmartTwinAdviceDetails::solution([
                'id'      => 'Insulate Facade Inside|SmartTwin:Facade_Interior_Insulation_PIR_Good_100',
                'name'    => 'Spouwmuurisolatie met PIR',
                'details' => [['id' => 'thickness', 'displayName' => 'Dikte', 'value' => 100, 'unit' => 'mm']],
            ])],
            ['facade' => [
                'part_ids'       => ['facade'],
                'insulated_area' => 40.98,
                'before'         => ['summary' => ['type' => 1, 'rc_value' => 1.3, 'area' => 40.98], 'lines' => []],
                'after'          => ['summary' => ['type' => 1, 'rc_value' => 2.58, 'area' => 40.98], 'lines' => []],
            ]],
        );
        UserActionPlanAdvice::withoutGlobalScopes()
            ->where('user_action_plan_advisable_id', $card->user_action_plan_advisable_id)
            ->update(['extra' => json_encode($details->intoExtra(null))]);

        $this->woonplan()
            ->assertSee(__('cooperation/frontend/tool.my-plan.smart-twin-details.title'))
            ->assertSee('Spouwmuurisolatie met PIR')
            ->assertSee('Dikte: 100 mm')
            ->assertSee(__('cooperation/frontend/tool.my-plan.smart-twin-details.elements.facade'))
            // SmartTwin's numbers as given.
            ->assertSee('1,3 m²K/W')
            ->assertSee('2,58 m²K/W')
            ->assertSee('40,98 m²');
    }
}
