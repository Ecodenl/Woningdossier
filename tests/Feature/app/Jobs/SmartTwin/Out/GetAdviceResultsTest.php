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
        $building->save();

        return [$building, $callback];
    }

    public function test_callback_is_cleared_without_fetching_when_smarttwin_calls_are_disabled(): void
    {
        config()->set('hoomdossier.services.smarttwin.enabled', false);

        [$building, $callback] = $this->buildingWithCallback();

        $this->mock(SmartTwinApi::class)->shouldNotReceive('advice');

        GetAdviceResults::dispatchSync($callback, $building->getKey());

        // Left in place, the nightly fallback cron would try the same callback again every night.
        $this->assertFalse($building->fresh()->hasSmartTwinCallback(EventType::RESIDENT_SCAN_FINISHED));
    }
}
