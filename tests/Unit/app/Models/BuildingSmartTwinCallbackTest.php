<?php

namespace Tests\Unit\app\Models;

use App\Enums\SmartTwin\EventType;
use App\Models\Building;
use Tests\TestCase;

final class BuildingSmartTwinCallbackTest extends TestCase
{
    private function residentData(string $dossierId): array
    {
        return ['DossierId' => $dossierId, 'EventType' => EventType::RESIDENT_SCAN_FINISHED->value];
    }

    private function coachData(string $dossierId): array
    {
        return ['DossierId' => $dossierId, 'EventType' => EventType::COACH_SCAN_FINISHED->value];
    }

    public function test_set_and_get_callback_per_event_type(): void
    {
        $building = new Building();
        $resident = $this->residentData('d-res');

        $building->setSmartTwinCallback(EventType::RESIDENT_SCAN_FINISHED, $resident);

        $this->assertTrue($building->hasSmartTwinCallback(EventType::RESIDENT_SCAN_FINISHED));
        $this->assertFalse($building->hasSmartTwinCallback(EventType::COACH_SCAN_FINISHED));
        $this->assertSame($resident, $building->getSmartTwinCallback(EventType::RESIDENT_SCAN_FINISHED));
        $this->assertNull($building->getSmartTwinCallback(EventType::COACH_SCAN_FINISHED));
    }

    public function test_set_is_latest_wins_and_holds_at_most_one_per_flow(): void
    {
        $building = new Building();

        $building->setSmartTwinCallback(EventType::RESIDENT_SCAN_FINISHED, $this->residentData('first'));
        $building->setSmartTwinCallback(EventType::COACH_SCAN_FINISHED, $this->coachData('coach'));
        $building->setSmartTwinCallback(EventType::RESIDENT_SCAN_FINISHED, $this->residentData('second'));

        $callbacks = $building->getSmartTwinCallbacks();

        $this->assertCount(2, $callbacks);
        $this->assertSame('second', $callbacks[EventType::RESIDENT_SCAN_FINISHED->value]['DossierId']);
        $this->assertSame('coach', $callbacks[EventType::COACH_SCAN_FINISHED->value]['DossierId']);
    }

    public function test_finish_removes_only_that_flow(): void
    {
        $building = new Building();
        $building->setSmartTwinCallback(EventType::RESIDENT_SCAN_FINISHED, $this->residentData('r'));
        $building->setSmartTwinCallback(EventType::COACH_SCAN_FINISHED, $this->coachData('c'));

        $building->finishSmartTwinCallback(EventType::RESIDENT_SCAN_FINISHED);

        $this->assertFalse($building->hasSmartTwinCallback(EventType::RESIDENT_SCAN_FINISHED));
        $this->assertTrue($building->hasSmartTwinCallback(EventType::COACH_SCAN_FINISHED));
    }

    public function test_finish_last_flow_collapses_to_null(): void
    {
        $building = new Building();
        $building->setSmartTwinCallback(EventType::RESIDENT_SCAN_FINISHED, $this->residentData('r'));

        $building->finishSmartTwinCallback(EventType::RESIDENT_SCAN_FINISHED);

        $this->assertNull($building->smarttwin_callback);
    }

    public function test_get_added_callbacks_returns_newly_added_flow(): void
    {
        $building = new Building();
        $building->setSmartTwinCallback(EventType::RESIDENT_SCAN_FINISHED, $this->residentData('r1'));
        $building->syncOriginal();

        $building->setSmartTwinCallback(EventType::COACH_SCAN_FINISHED, $this->coachData('c1'));

        $added = $building->getAddedSmartTwinCallbacks();

        $this->assertCount(1, $added);
        $this->assertSame('c1', $added[0]['DossierId']);
    }

    public function test_get_added_callbacks_detects_changed_payload_for_same_flow(): void
    {
        $building = new Building();
        $building->setSmartTwinCallback(EventType::RESIDENT_SCAN_FINISHED, $this->residentData('old'));
        $building->syncOriginal();

        // Resident re-scans: same flow, different dossier/payload -> counts as added.
        $building->setSmartTwinCallback(EventType::RESIDENT_SCAN_FINISHED, $this->residentData('new'));

        $added = $building->getAddedSmartTwinCallbacks();

        $this->assertCount(1, $added);
        $this->assertSame('new', $added[0]['DossierId']);
    }

    public function test_get_added_callbacks_is_empty_when_nothing_changed(): void
    {
        $building = new Building();
        $building->setSmartTwinCallback(EventType::RESIDENT_SCAN_FINISHED, $this->residentData('r1'));
        $building->syncOriginal();

        $this->assertSame([], $building->getAddedSmartTwinCallbacks());
    }
}
