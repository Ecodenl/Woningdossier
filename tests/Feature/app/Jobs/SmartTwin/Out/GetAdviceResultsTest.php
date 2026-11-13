<?php

namespace Tests\Feature\app\Jobs\SmartTwin\Out;

use App\Enums\SmartTwin\EventType;
use App\Jobs\SmartTwin\Out\GetAdviceResults;
use App\Models\Building;
use App\Services\SmartTwin\Api\SmartTwinApi;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class GetAdviceResultsTest extends TestCase
{
    use RefreshDatabase;

    private function buildingWithCallback(): array
    {
        $callback = [
            'EventType' => EventType::RESIDENT_SCAN_FINISHED->value,
            'DossierId' => 'dossier-guid-1',
        ];

        $building = Building::factory()->withUser()->create();
        $building->setSmartTwinCallback(EventType::RESIDENT_SCAN_FINISHED, $callback);
        // Quietly: a saved callback dispatches this very job through the observer, and the job is
        // what is under test here.
        $building->saveQuietly();

        return [$building, $callback];
    }

    public function test_callback_is_cleared_without_fetching_when_smarttwin_calls_are_disabled(): void
    {
        config()->set('hoomdossier.services.smarttwin.enabled', false);

        $this->mock(SmartTwinApi::class)->shouldNotReceive('advice');

        [$building, $callback] = $this->buildingWithCallback();

        GetAdviceResults::dispatchSync($callback, $building->getKey());

        // Left in place, the nightly fallback cron would try the same callback again every night.
        $this->assertFalse($building->fresh()->hasSmartTwinCallback(EventType::RESIDENT_SCAN_FINISHED));
    }

    public function test_callback_is_cleared_without_fetching_for_a_cooperation_that_does_not_take_part(): void
    {
        config()->set('hoomdossier.services.smarttwin.enabled', true);

        $this->mock(SmartTwinApi::class)->shouldNotReceive('advice');

        [$building, $callback] = $this->buildingWithCallback();
        $building->user->cooperation->update(['smarttwin_enabled' => false]);

        GetAdviceResults::dispatchSync($callback, $building->getKey());

        $this->assertFalse($building->fresh()->hasSmartTwinCallback(EventType::RESIDENT_SCAN_FINISHED));
    }
}
