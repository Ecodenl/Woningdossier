<?php

namespace Tests\Feature\app\Livewire\Cooperation\Frontend\Tool\SimpleScan\MyPlan;

use App\Enums\AdviceSource;
use App\Helpers\HoomdossierSession;
use App\Helpers\RoleHelper;
use App\Jobs\RefreshRegulationsForUserActionPlanAdvice;
use App\Jobs\SmartTwin\Out\GetBuildingData;
use App\Livewire\Cooperation\Frontend\Tool\SimpleScan\CustomChanges;
use App\Livewire\Cooperation\Frontend\Tool\SimpleScan\MyPlan\Form;
use App\Models\Building;
use App\Models\Cooperation;
use App\Models\CustomMeasureApplication;
use App\Models\InputSource;
use App\Models\MeasureApplication;
use App\Models\Role;
use App\Models\Scan;
use App\Models\User;
use App\Models\UserActionPlanAdvice;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * An own measure that was first refused: once it is saved, what was refused is over and done with.
 * The measure keeps the index the form had while it was new, so an error left behind shows up on
 * the saved measure when it is opened again.
 */
final class CustomMeasureValidationTest extends TestCase
{
    use RefreshDatabase;

    public bool $seed = true;
    public string $seeder = DatabaseSeeder::class;

    private const string INFO = 'customMeasureApplicationsFormData.0.info';
    private const string COSTS_FROM = 'customMeasureApplicationsFormData.0.costs.from';
    private const string COSTS_TO = 'customMeasureApplicationsFormData.0.costs.to';

    private Building $building;

    protected function setUp(): void
    {
        parent::setUp();

        Bus::fake([GetBuildingData::class, RefreshRegulationsForUserActionPlanAdvice::class]);

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

    private function form(): Testable
    {
        return Livewire::test(Form::class, ['scan' => Scan::quick(), 'building' => $this->building]);
    }

    /**
     * The report of the customer: no description, so it is refused; a description, so it is saved.
     */
    private function refusedThenSaved(array $costs = ['from' => '1000', 'to' => '2000']): Testable
    {
        return $this->form()
            ->set('customMeasureApplicationsFormData.0.name', 'Nieuwe vloer')
            ->set('customMeasureApplicationsFormData.0.costs.from', $costs['from'])
            ->set('customMeasureApplicationsFormData.0.costs.to', $costs['to'])
            ->call('save', 0)
            ->assertHasErrors([self::INFO => 'required'])
            ->set(self::INFO, 'Een beschrijving')
            ->call('save', 0);
    }

    private function master(): CustomMeasureApplication
    {
        return CustomMeasureApplication::forInputSource(InputSource::master())
            ->where('building_id', $this->building->id)
            ->sole();
    }

    private function advice(): UserActionPlanAdvice
    {
        return UserActionPlanAdvice::forInputSource(InputSource::master())
            ->where('user_id', $this->building->user_id)
            ->where('user_action_plan_advisable_type', CustomMeasureApplication::class)
            ->sole();
    }

    public function test_a_missing_description_is_refused(): void
    {
        $this->form()
            ->set('customMeasureApplicationsFormData.0.name', 'Nieuwe vloer')
            ->set('customMeasureApplicationsFormData.0.costs.from', '1000')
            ->call('save', 0)
            ->assertHasErrors([self::INFO => 'required'])
            ->assertSeeHtml('form-error-label');

        $this->assertSame(0, CustomMeasureApplication::allInputSources()->where('building_id', $this->building->id)->count());
    }

    public function test_saving_clears_the_error_of_the_refused_attempt(): void
    {
        $this->refusedThenSaved()
            ->assertHasNoErrors()
            ->assertDispatched('close-modal')
            ->assertDontSeeHtml('form-error-label');

        $this->assertSame('Een beschrijving', $this->master()->info);
    }

    public function test_the_saved_measure_opens_again_without_a_red_field_and_can_be_updated(): void
    {
        $this->refusedThenSaved()
            ->assertSet(self::INFO, 'Een beschrijving')
            ->set(self::INFO, 'Een andere beschrijving')
            ->assertDontSeeHtml('form-error-label')
            ->call('save', 0)
            ->assertHasNoErrors()
            ->assertDispatched('close-modal');

        $this->assertSame('Een andere beschrijving', $this->master()->info);
    }

    public function test_an_open_ended_price_survives_the_refused_attempt(): void
    {
        $this->refusedThenSaved(['from' => '1000', 'to' => ''])
            ->assertHasNoErrors(self::COSTS_TO)
            ->assertDispatched('close-modal');

        $advice = $this->advice();

        $this->assertEquals(1000, $advice->costs['from']);
        $this->assertArrayNotHasKey('to', $advice->costs);
    }

    public function test_a_missing_price_stays_required_after_the_refused_attempt(): void
    {
        $this->form()
            ->set('customMeasureApplicationsFormData.0.name', 'Nieuwe vloer')
            ->call('save', 0)
            ->assertHasErrors([self::INFO => 'required', self::COSTS_FROM => 'required_if'])
            ->assertSet(self::COSTS_FROM, null)
            ->set(self::INFO, 'Een beschrijving')
            ->call('save', 0)
            ->assertHasErrors([self::COSTS_FROM => 'required_if'])
            ->assertHasNoErrors(self::INFO)
            ->assertNotDispatched('close-modal');

        $this->assertSame(0, CustomMeasureApplication::allInputSources()->where('building_id', $this->building->id)->count());
    }

    public function test_the_refused_price_can_be_filled_in_and_saved(): void
    {
        $this->form()
            ->set('customMeasureApplicationsFormData.0.name', 'Nieuwe vloer')
            ->call('save', 0)
            ->assertHasErrors([self::COSTS_FROM => 'required_if'])
            ->set(self::INFO, 'Een beschrijving')
            ->set(self::COSTS_FROM, '1000')
            ->call('save', 0)
            ->assertHasNoErrors()
            ->assertDispatched('close-modal');

        $this->assertEquals(1000, $this->advice()->costs['from']);
    }

    public function test_no_costs_needs_no_price_after_the_refused_attempt(): void
    {
        $this->form()
            ->set('customMeasureApplicationsFormData.0.name', 'Nieuwe vloer')
            ->set('customMeasureApplicationsFormData.0.hide_costs', true)
            ->call('save', 0)
            ->assertHasErrors([self::INFO => 'required'])
            ->assertHasNoErrors(self::COSTS_FROM)
            ->set(self::INFO, 'Een beschrijving')
            ->call('save', 0)
            ->assertHasNoErrors()
            ->assertDispatched('close-modal');

        $this->assertNull($this->advice()->costs);
    }

    public function test_the_same_goes_for_a_woonplan_that_is_smart_twins(): void
    {
        config()->set('hoomdossier.services.smarttwin.enabled', true);

        $measure = MeasureApplication::findByShort('cavity-wall-insulation');
        UserActionPlanAdvice::withoutEvents(fn () => UserActionPlanAdvice::withoutGlobalScopes()->forceCreate([
            'user_id'                         => $this->building->user_id,
            'input_source_id'                 => InputSource::master()->id,
            'user_action_plan_advisable_type' => MeasureApplication::class,
            'user_action_plan_advisable_id'   => $measure->id,
            'step_id'                         => $measure->step_id,
            'costs'                           => ['from' => null, 'to' => 1000],
            'category'                        => 'to-do',
            'visible'                         => true,
            'source'                          => AdviceSource::SMART_TWIN->value,
        ]));

        $this->refusedThenSaved()
            ->assertHasNoErrors()
            ->assertDontSeeHtml('form-error-label')
            ->set(self::INFO, 'Een andere beschrijving')
            ->call('save', 0)
            ->assertHasNoErrors();

        $this->assertSame('Een andere beschrijving', $this->master()->info);
    }

    public function test_the_same_goes_for_the_own_measures_in_the_scan(): void
    {
        Livewire::test(CustomChanges::class, ['scan' => Scan::quick()])
            ->set('customMeasureApplicationsFormData.0.name', 'Nieuwe vloer')
            ->set('customMeasureApplicationsFormData.0.costs.from', '1000')
            ->call('save', 0)
            ->assertHasErrors([self::INFO => 'required'])
            ->set(self::INFO, 'Een beschrijving')
            ->call('save', 0)
            ->assertHasNoErrors()
            ->assertDontSeeHtml('form-error-label');

        $this->assertSame('Een beschrijving', $this->master()->info);
    }

    public function test_a_measure_refused_again_on_updating_shows_the_error_again(): void
    {
        $this->refusedThenSaved()
            ->set(self::INFO, '')
            ->call('save', 0)
            ->assertHasErrors([self::INFO => 'required'])
            ->assertSeeHtml('form-error-label');

        $this->assertSame('Een beschrijving', $this->master()->info);
    }
}
