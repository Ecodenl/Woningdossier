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
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
}
