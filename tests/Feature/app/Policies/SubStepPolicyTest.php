<?php

namespace Tests\Feature\app\Policies;

use App\Helpers\HoomdossierSession;
use App\Helpers\ScanAvailabilityHelper;
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
 * asking them — for a cooperation that takes part, and only there. The lite scan's counterparts of
 * those steps stop too, but only for a building that also has the quick scan: one with only the lite
 * scan is never sent to SmartTwin (decision 2026-10-15-smarttwin-woninggegevens-building-data, L1).
 */
final class SubStepPolicyTest extends TestCase
{
    use RefreshDatabase;

    public $seed = true;
    public $seeder = DatabaseSeeder::class;

    private function canShowBuildingData(Cooperation $cooperation): bool
    {
        return $this->canShow('building-data', $cooperation, [Scan::QUICK]);
    }

    /**
     * @param  array<int, string>  $scans  The simple scans the cooperation, and so the building, has.
     * @param  array<int, string>  $offForBuilding  Of those, the ones switched off for the building itself.
     */
    private function canShow(string $stepShort, Cooperation $cooperation, array $scans, array $offForBuilding = []): bool
    {
        // Replaced, not added to: a new cooperation is given every scan.
        $cooperation->scans()->sync(array_map(fn (string $short) => Scan::findByShort($short)->id, $scans));

        $user = User::factory()->withAccount()->asResident()->create(['cooperation_id' => $cooperation->id]);
        $building = Building::factory()->create(['user_id' => $user->id]);
        HoomdossierSession::setCooperation($cooperation);

        foreach ($offForBuilding as $short) {
            ScanAvailabilityHelper::setAvailability($building, Scan::findByShort($short), false);
        }

        // The first sub step of a step SmartTwin provides carries no conditions of its own, so the
        // policy is all that decides.
        $subStep = SubStep::where('step_id', Step::findByShort($stepShort)->id)
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

    /**
     * @return array<string, array{0: string}>
     */
    public static function liteCounterparts(): array
    {
        return [
            'building data' => ['building-data-lite'],
            'usage'         => ['usage-lite-scan'],
            'current state' => ['residential-status-lite'],
        ];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('liteCounterparts')]
    public function test_a_lite_counterpart_is_hidden_for_a_building_that_also_has_the_quick_scan(string $stepShort): void
    {
        config()->set('hoomdossier.services.smarttwin.enabled', true);

        $this->assertFalse($this->canShow($stepShort, Cooperation::factory()->create(), [Scan::QUICK, Scan::LITE]));
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('liteCounterparts')]
    public function test_a_lite_counterpart_is_asked_for_a_building_with_only_the_lite_scan(string $stepShort): void
    {
        config()->set('hoomdossier.services.smarttwin.enabled', true);

        $this->assertTrue($this->canShow($stepShort, Cooperation::factory()->create(), [Scan::LITE]));
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('liteCounterparts')]
    public function test_a_lite_counterpart_is_asked_when_the_quick_scan_is_off_for_the_building(string $stepShort): void
    {
        config()->set('hoomdossier.services.smarttwin.enabled', true);

        // The cooperation offers both scans, but this building only gets the lite scan, so it is
        // never sent to SmartTwin either.
        $this->assertTrue($this->canShow($stepShort, Cooperation::factory()->create(), [Scan::QUICK, Scan::LITE], [Scan::QUICK]));
    }

    public function test_a_lite_counterpart_is_asked_in_a_cooperation_that_does_not_take_part(): void
    {
        config()->set('hoomdossier.services.smarttwin.enabled', true);

        $this->assertTrue($this->canShow('building-data-lite', Cooperation::factory()->create(['smarttwin_enabled' => false]), [Scan::QUICK, Scan::LITE]));
    }

    public function test_the_rest_of_the_lite_scan_is_asked(): void
    {
        config()->set('hoomdossier.services.smarttwin.enabled', true);

        $this->assertTrue($this->canShow('living-requirements-lite', Cooperation::factory()->create(), [Scan::QUICK, Scan::LITE]));
    }
}
