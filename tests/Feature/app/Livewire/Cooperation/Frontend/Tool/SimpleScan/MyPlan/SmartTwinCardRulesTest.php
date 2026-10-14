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
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * The card rules of a SmartTwin woonplan, enforced by the woonplan itself: a package card stays in
 * "Nu aanpakken" and out of the trash, an own or small measure moves freely, and into "In orde"
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
                'costs'                           => json_encode(['from' => null, 'to' => 1000]),
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
            ->assertDispatched('open-executed', id: $card->id);

        // Not moved: it moves once the popup is saved.
        $this->assertSame(['to-do'], $this->categoriesOf($card));
    }

    public function test_a_package_card_does_not_go_in_the_trash(): void
    {
        $card = $this->packageCard();

        $this->woonplan()->call('cardTrashed', 'to-do', (string) $card->id);

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
        $this->assertFalse($packageCard['trashable']);

        $this->assertFalse($smallCard['in_package']);
        $this->assertSame(['complete', 'to-do', 'later'], $smallCard['targets']);
        $this->assertTrue($smallCard['trashable']);

        $woonplan->assertSee(__('cooperation/frontend/tool.my-plan.cards.package-label'));
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

        $woonplan->call('cardMoved', 'to-do', 'complete', (string) $card->id, '0');
        $this->assertSame('complete', $card->fresh()->category);
    }
}
