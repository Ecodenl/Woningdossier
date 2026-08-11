<?php

namespace Tests\Feature\app\Http\Controllers\Api\V1;

use App\Enums\SmartTwin\EventType;
use App\Events\SmartTwinCallbackReceived;
use App\Helpers\Models\BuildingSettingHelper;
use App\Http\Middleware\Api\SmartTwinSigned;
use App\Models\Building;
use App\Models\BuildingSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

final class SmartTwinWebhookTest extends TestCase
{
    use RefreshDatabase;

    private function makeBuildingWithDossier(string $dossierId): Building
    {
        $building = Building::factory()->create();

        $setting = new BuildingSetting();
        $setting->building_id = $building->getKey();
        $setting->short = BuildingSettingHelper::SHORT_SMARTTWIN_DOSSIER_ID;
        $setting->value = $dossierId;
        $setting->save();

        return $building;
    }

    private function postWebhook(string $dossierId, string $eventType): TestResponse
    {
        return $this->withoutMiddleware(SmartTwinSigned::class)
            ->postJson(route('api.v1.smarttwin.store'), [
                'data' => [
                    'DossierId' => $dossierId,
                    'EventType' => $eventType,
                ],
            ]);
    }

    public function test_webhook_stores_callback_keyed_by_event_type(): void
    {
        $dossierId = 'dossier-guid-1';
        $building = $this->makeBuildingWithDossier($dossierId);

        $this->postWebhook($dossierId, EventType::RESIDENT_SCAN_FINISHED->value)->assertNoContent();

        $callbacks = $building->fresh()->getSmartTwinCallbacks();

        $this->assertArrayHasKey(EventType::RESIDENT_SCAN_FINISHED->value, $callbacks);
        $this->assertSame($dossierId, $callbacks[EventType::RESIDENT_SCAN_FINISHED->value]['DossierId']);
    }

    public function test_webhook_is_latest_wins_and_holds_at_most_one_per_flow(): void
    {
        $dossierId = 'dossier-guid-1';
        $building = $this->makeBuildingWithDossier($dossierId);

        $this->postWebhook($dossierId, EventType::RESIDENT_SCAN_FINISHED->value)->assertNoContent();
        $this->postWebhook($dossierId, EventType::COACH_SCAN_FINISHED->value)->assertNoContent();
        // Second resident webhook overwrites the first rather than appending.
        $this->postWebhook($dossierId, EventType::RESIDENT_SCAN_FINISHED->value)->assertNoContent();

        $callbacks = $building->fresh()->getSmartTwinCallbacks();

        $this->assertCount(2, $callbacks);
        $this->assertArrayHasKey(EventType::RESIDENT_SCAN_FINISHED->value, $callbacks);
        $this->assertArrayHasKey(EventType::COACH_SCAN_FINISHED->value, $callbacks);
    }

    public function test_webhook_ignores_unknown_event_type(): void
    {
        $dossierId = 'dossier-guid-1';
        $building = $this->makeBuildingWithDossier($dossierId);

        $this->postWebhook($dossierId, 'totally.unknown.event')->assertNoContent();

        $this->assertNull($building->fresh()->smarttwin_callback);
    }

    public function test_saving_a_new_callback_dispatches_event_with_added_callbacks(): void
    {
        Event::fake([SmartTwinCallbackReceived::class]);

        $building = Building::factory()->create();

        $building->setSmartTwinCallback(EventType::RESIDENT_SCAN_FINISHED, [
            'DossierId' => 'd1',
            'EventType' => EventType::RESIDENT_SCAN_FINISHED->value,
        ]);
        $building->save();

        Event::assertDispatched(
            SmartTwinCallbackReceived::class,
            fn (SmartTwinCallbackReceived $event) => $event->building->is($building)
                && count($event->addedCallbacks) === 1
                && $event->addedCallbacks[0]['DossierId'] === 'd1',
        );
    }
}
