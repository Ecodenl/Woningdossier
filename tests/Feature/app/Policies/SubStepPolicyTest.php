<?php

namespace Tests\Feature\app\Policies;

use App\Helpers\HoomdossierSession;
use App\Models\Building;
use App\Models\Cooperation;
use App\Models\Scan;
use App\Models\Step;
use App\Models\SubStep;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * SmartTwin asks the technical questions of the quick scan in its own tool, so Hoomdossier stops
 * asking them — for a cooperation that takes part, and only there.
 */
final class SubStepPolicyTest extends TestCase
{
    use RefreshDatabase;

    public $seed = true;
    public $seeder = DatabaseSeeder::class;

    private function canShowBuildingData(Cooperation $cooperation): bool
    {
        $cooperation->scans()->syncWithoutDetaching([Scan::findByShort(Scan::QUICK)->id]);

        $user = User::factory()->withAccount()->asResident()->create(['cooperation_id' => $cooperation->id]);
        $building = Building::factory()->create(['user_id' => $user->id]);
        HoomdossierSession::setCooperation($cooperation);

        // The first sub step of a step SmartTwin provides carries no conditions of its own, so the
        // policy is all that decides.
        $subStep = SubStep::where('step_id', Step::findByShort('building-data')->id)
            ->whereNull('conditions')
            ->orderBy('order')
            ->firstOrFail();

        return $user->account->can('show', [$subStep, $building]);
    }

    public function test_a_step_smart_twin_provides_is_hidden_for_a_cooperation_that_takes_part(): void
    {
        config()->set('hoomdossier.services.smarttwin.enabled', true);

        $this->assertFalse($this->canShowBuildingData(Cooperation::factory()->create()));
    }

    public function test_the_step_is_asked_for_a_cooperation_that_does_not_take_part(): void
    {
        config()->set('hoomdossier.services.smarttwin.enabled', true);

        $this->assertTrue($this->canShowBuildingData(Cooperation::factory()->create(['smarttwin_enabled' => false])));
    }

    public function test_the_step_is_asked_when_smart_twin_is_off_for_the_environment(): void
    {
        config()->set('hoomdossier.services.smarttwin.enabled', false);

        $this->assertTrue($this->canShowBuildingData(Cooperation::factory()->create()));
    }
}
