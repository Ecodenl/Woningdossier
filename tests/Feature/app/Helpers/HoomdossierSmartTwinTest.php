<?php

namespace Tests\Feature\app\Helpers;

use App\Helpers\Hoomdossier;
use App\Models\Building;
use App\Models\Cooperation;
use App\Models\Scan;
use App\Models\User;
use Database\Seeders\ScansTableSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Two switches decide whether SmartTwin plays a part: SMARTTWIN_ENABLED for the environment, and
 * the one per cooperation. The environment switch wins.
 */
final class HoomdossierSmartTwinTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(ScansTableSeeder::class);
        config()->set('hoomdossier.services.smarttwin.enabled', true);
    }

    private function buildingFor(Cooperation $cooperation): Building
    {
        $cooperation->scans()->attach(Scan::findByShort(Scan::QUICK)->id);

        $user = User::factory()->withAccount()->create(['cooperation_id' => $cooperation->id]);

        return Building::factory()->create(['user_id' => $user->id]);
    }

    public function test_a_cooperation_takes_part_by_default(): void
    {
        // Not read back from the database: the model default has to carry this on its own.
        $cooperation = Cooperation::factory()->create();

        $this->assertTrue(Hoomdossier::smartTwinEnabledFor($cooperation));
        $this->assertTrue(Hoomdossier::smartTwinEnabledFor($cooperation->fresh()));
    }

    public function test_a_cooperation_can_be_switched_off(): void
    {
        $cooperation = Cooperation::factory()->create(['smarttwin_enabled' => false]);

        $this->assertFalse(Hoomdossier::smartTwinEnabledFor($cooperation));
        $this->assertTrue(Hoomdossier::hasSmartTwinApi());
    }

    public function test_the_environment_switch_wins_over_the_cooperation(): void
    {
        config()->set('hoomdossier.services.smarttwin.enabled', false);

        $cooperation = Cooperation::factory()->create(['smarttwin_enabled' => true]);

        $this->assertFalse(Hoomdossier::smartTwinEnabledFor($cooperation));
        $this->assertFalse(Hoomdossier::hasSmartTwinApi());
    }

    public function test_no_cooperation_means_no_smart_twin(): void
    {
        $this->assertFalse(Hoomdossier::smartTwinEnabledFor(null));
    }

    public function test_the_woonplan_follows_the_cooperation_of_the_building(): void
    {
        $on = $this->buildingFor(Cooperation::factory()->create());
        $off = $this->buildingFor(Cooperation::factory()->create(['smarttwin_enabled' => false]));

        $this->assertTrue(Hoomdossier::smartTwinFillsWoonplanFor($on));
        $this->assertFalse(Hoomdossier::smartTwinFillsWoonplanFor($off));
    }

    public function test_smart_twin_drives_only_the_quick_scan(): void
    {
        $building = $this->buildingFor(Cooperation::factory()->create());

        $this->assertTrue(Hoomdossier::smartTwinDrivesScan($building, Scan::findByShort(Scan::QUICK)));
        // Nothing of the lite scan or the expert scan comes back from SmartTwin, so their required
        // questions stay required.
        $this->assertFalse(Hoomdossier::smartTwinDrivesScan($building, Scan::findByShort(Scan::LITE)));
        $this->assertFalse(Hoomdossier::smartTwinDrivesScan($building, Scan::findByShort(Scan::EXPERT)));
    }

    public function test_smart_twin_does_not_drive_the_scan_of_a_cooperation_that_is_switched_off(): void
    {
        $building = $this->buildingFor(Cooperation::factory()->create(['smarttwin_enabled' => false]));

        $this->assertFalse(Hoomdossier::smartTwinDrivesScan($building, Scan::findByShort(Scan::QUICK)));
    }

    public function test_the_cooperation_of_a_building_is_found_without_a_session(): void
    {
        $cooperation = Cooperation::factory()->create();
        $building = $this->buildingFor($cooperation);

        $this->assertTrue(Hoomdossier::cooperationOf(Building::find($building->id))->is($cooperation));
    }
}
