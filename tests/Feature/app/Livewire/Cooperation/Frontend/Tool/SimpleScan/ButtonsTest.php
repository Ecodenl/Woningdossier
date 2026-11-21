<?php

namespace Tests\Feature\app\Livewire\Cooperation\Frontend\Tool\SimpleScan;

use App\Helpers\HoomdossierSession;
use App\Helpers\RoleHelper;
use App\Livewire\Cooperation\Frontend\Tool\SimpleScan\Buttons;
use App\Models\Building;
use App\Models\Cooperation;
use App\Models\InputSource;
use App\Models\Role;
use App\Models\Scan;
use App\Models\Step;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;
use Tests\TestCase;

final class ButtonsTest extends TestCase
{
    use RefreshDatabase;

    public bool $seed = true;
    public string $seeder = DatabaseSeeder::class;

    public function test_the_previous_url_holds_the_cooperation_without_it_as_url_default(): void
    {
        $cooperation = Cooperation::factory()->create();
        $scan = Scan::quick();
        $cooperation->scans()->attach($scan->id);

        $resident = User::factory()->withAccount()->asResident()
            ->create(['cooperation_id' => $cooperation->id, 'allow_access' => true]);
        $building = Building::factory()->create(['user_id' => $resident->id]);

        $this->actingAs($resident->account);
        HoomdossierSession::setCooperation($cooperation);
        HoomdossierSession::setHoomdossierSessions(
            $building,
            InputSource::resident(),
            InputSource::resident(),
            Role::findByName(RoleHelper::ROLE_RESIDENT),
        );

        // The second sub step of the first step, so there is a sub step to go back to.
        $step = $scan->steps()->orderBy('order')->first();
        $subStep = $step->subSteps()->orderBy('order')->skip(1)->first();

        // No URL::defaults(['cooperation' => ...]): not set where the cooperation middleware does not run.
        $buttons = Livewire::test(Buttons::class, [
            'scan'                   => $scan,
            'step'                   => $step,
            'subStepOrQuestionnaire' => $subStep,
        ]);

        // The cooperation is the subdomain.
        $this->assertStringContainsString("//{$cooperation->slug}.", $buttons->get('previousUrl'));
    }

    public function test_previous_steps_over_a_step_that_is_hidden_in_its_entirety(): void
    {
        $buttons = $this->buttonsOnFirstSubStepOfLiteStepForSmartTwinBuilding('small-measures-lite');

        // residential-status-lite comes before, but SmartTwin answers it for this building.
        $expectedStep = Step::findByShort('living-requirements-lite');
        $expectedSubStep = $expectedStep->subSteps()->orderByDesc('order')->first();

        $this->assertSame($expectedStep->id, $buttons->get('previousStep')->id);
        $this->assertSame($expectedSubStep->id, $buttons->get('previousSubStep')->id);
        $this->assertStringEndsWith("/{$expectedStep->slug}/{$expectedSubStep->slug}", $buttons->get('previousUrl'));
    }

    public function test_there_is_no_previous_when_every_step_before_is_hidden(): void
    {
        // building-data-lite and usage-lite-scan come before, and SmartTwin answers both.
        $buttons = $this->buttonsOnFirstSubStepOfLiteStepForSmartTwinBuilding('living-requirements-lite');

        $this->assertNull($buttons->get('previousSubStep'));
        $this->assertNull($buttons->get('previousQuestionnaire'));
        $this->assertSame('', $buttons->get('previousUrl'));
    }

    /**
     * A building that has the lite scan next to the quick scan, in a cooperation that takes part in
     * SmartTwin, so the lite counterparts of the steps SmartTwin provides are hidden for it.
     */
    private function buttonsOnFirstSubStepOfLiteStepForSmartTwinBuilding(string $stepShort): Testable
    {
        config()->set('hoomdossier.services.smarttwin.enabled', true);

        $cooperation = Cooperation::factory()->create();
        $cooperation->scans()->sync([Scan::quick()->id, Scan::lite()->id]);

        $resident = User::factory()->withAccount()->asResident()
            ->create(['cooperation_id' => $cooperation->id, 'allow_access' => true]);
        $building = Building::factory()->create(['user_id' => $resident->id]);

        $this->actingAs($resident->account);
        HoomdossierSession::setCooperation($cooperation);
        HoomdossierSession::setHoomdossierSessions(
            $building,
            InputSource::resident(),
            InputSource::resident(),
            Role::findByName(RoleHelper::ROLE_RESIDENT),
        );

        $step = Step::findByShort($stepShort);

        return Livewire::test(Buttons::class, [
            'scan'                   => Scan::lite(),
            'step'                   => $step,
            'subStepOrQuestionnaire' => $step->subSteps()->orderBy('order')->first(),
        ]);
    }
}
