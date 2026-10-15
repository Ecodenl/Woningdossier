<?php

namespace Tests\Feature\app\Helpers;

use App\Enums\AdviceSource;
use App\Helpers\Hoomdossier;
use App\Models\Building;
use App\Models\Cooperation;
use App\Models\InputSource;
use App\Models\MeasureApplication;
use App\Models\Scan;
use App\Models\User;
use App\Models\UserActionPlanAdvice;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * The switch a building's woonplan goes over to SmartTwin on: its cooperation takes part and
 * SmartTwin advised it at least once. Before that the woonplan works as it always did, also in a
 * cooperation that switched SmartTwin on.
 */
final class SmartTwinSwitchTest extends TestCase
{
    use RefreshDatabase;

    public $seed = true;
    public $seeder = DatabaseSeeder::class;

    private Cooperation $cooperation;
    private Building $building;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('hoomdossier.services.smarttwin.enabled', true);

        $this->cooperation = Cooperation::factory()->create();
        $this->cooperation->scans()->attach(Scan::findByShort(Scan::QUICK)->id);

        $user = User::factory()->withAccount()->asResident()->create(['cooperation_id' => $this->cooperation->id]);
        $this->building = Building::factory()->create(['user_id' => $user->id]);
    }

    private function advice(string $inputSourceShort, ?AdviceSource $source): void
    {
        $measure = MeasureApplication::findByShort('cavity-wall-insulation');

        UserActionPlanAdvice::withoutGlobalScopes()->create([
            'user_id'                         => $this->building->user_id,
            'input_source_id'                 => InputSource::findByShort($inputSourceShort)->id,
            'user_action_plan_advisable_type' => MeasureApplication::class,
            'user_action_plan_advisable_id'   => $measure->id,
            'step_id'                         => $measure->step_id,
            'costs'                           => ['from' => null, 'to' => 1000],
            'source'                          => $source,
        ]);
    }

    private function switched(): bool
    {
        return Hoomdossier::smartTwinOwnsWoonplanFor($this->building->fresh());
    }

    public function test_a_cooperation_that_takes_part_is_not_enough(): void
    {
        // A finished dossier from before SmartTwin: its woonplan keeps working as it did.
        $this->advice(InputSource::RESIDENT_SHORT, null);

        $this->assertFalse($this->switched());
    }

    public function test_the_first_smart_twin_advice_switches_the_building(): void
    {
        // Whichever input source it came in through.
        $this->advice(InputSource::COACH_SHORT, AdviceSource::SMART_TWIN);

        $this->assertTrue($this->switched());
    }

    public function test_switching_the_cooperation_off_switches_back_at_once(): void
    {
        $this->advice(InputSource::RESIDENT_SHORT, AdviceSource::SMART_TWIN);

        // Before UndoSmartTwinForBuilding has removed the SmartTwin advices.
        $this->cooperation->update(['smarttwin_enabled' => false]);

        $this->assertFalse($this->switched());
    }

    public function test_without_the_environment_switch_nothing_switches(): void
    {
        $this->advice(InputSource::RESIDENT_SHORT, AdviceSource::SMART_TWIN);

        config()->set('hoomdossier.services.smarttwin.enabled', false);

        $this->assertFalse($this->switched());
    }
}
