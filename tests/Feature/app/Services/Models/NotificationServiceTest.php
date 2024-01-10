<?php

namespace Tests\Feature\app\Services\Models;

use App\Helpers\Str;
use App\Models\Building;
use App\Models\InputSource;
use App\Models\Notification;
use App\Services\Models\NotificationService;
use Database\Seeders\InputSourcesTableSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_set_notifications_active()
    {
        $this->seed(InputSourcesTableSeeder::class);
        $building = Building::factory()->withUser()->create();

        $residentInputSource = InputSource::resident();
        $masterInputSource = InputSource::master();

        $uuid1 = Str::uuid();
        $uuid2 = Str::uuid();

        $notificationService = NotificationService::init()
            ->forBuilding($building)
            ->forInputSource($residentInputSource)
            ->setType('test');

        $notificationService->setActive([$uuid1, $uuid2]);

        // Due to master, we should now have 4 active notifications.
        $this->assertDatabaseCount('notifications', 4);
        foreach ([$residentInputSource, $masterInputSource] as $inputSource) {
            foreach ([$uuid1, $uuid2] as $uuid) {
                $this->assertDatabaseHas('notifications', [
                    'building_id' => $building->id,
                    'input_source_id' => $inputSource->id,
                    'uuid' => $uuid,
                    'type' => 'test',
                ]);
            }
        }
    }

    public function test_notifications_are_active()
    {
        $this->seed(InputSourcesTableSeeder::class);
        $building = Building::factory()->withUser()->create();
        
        $residentInputSource = InputSource::resident();
        $masterInputSource = InputSource::master();

        $uuid1 = Str::uuid();
        $uuid2 = Str::uuid();
        $uuid3 = Str::uuid();
        $uuid4 = Str::uuid();

        $notificationService = NotificationService::init()
            ->forBuilding($building)
            ->forInputSource($residentInputSource)
            ->setType('test');

        $notificationService->setActive([$uuid1, $uuid2]);

        // Assert we have notifications for this type.
        $this->assertTrue($notificationService->isActive());
        $this->assertTrue($notificationService->hasActiveTypes(['test']));
        // Assert we have notifications for this type + uuid1.
        $this->assertTrue($notificationService->setUuid($uuid1)->isActive());
        $this->assertTrue($notificationService->hasActiveTypes(['test']));
        // Assert we have notifications for this type + uuid2.
        $this->assertTrue($notificationService->setUuid($uuid2)->isActive());
        $this->assertTrue($notificationService->hasActiveTypes(['test']));

        // Assert we have one of types (we still have uuid2 "set", but that doesn't matter).
        $this->assertTrue($notificationService->hasActiveTypes(['test', 'not-existing', 'other-type']));

        // Assert we don't have one of types.
        $this->assertFalse($notificationService->hasActiveTypes(['not-existing', 'other-type']));

        // Create new notification with new uuid.
        $notificationService->setType('className')->setActive([$uuid3]);
        $this->assertTrue($notificationService->setUuid($uuid3)->isActive());
        $this->assertTrue($notificationService->hasActiveTypes(['test', 'className']));

        // Assert false if checked for this uuid on wrong type.
        $this->assertFalse($notificationService->setType('test')->isActive());
        $this->assertFalse($notificationService->hasActiveTypes(['test']));

        // Assert without uuid.
        $notificationService = NotificationService::init()
            ->forBuilding($building)
            ->forInputSource($residentInputSource)
            ->setType('test');

        $this->assertTrue($notificationService->hasActiveTypes(['test']));
        $this->assertTrue($notificationService->hasActiveTypes(['className']));
        $this->assertTrue($notificationService->hasActiveTypes(['test', 'className']));

        $this->assertFalse($notificationService->hasActiveTypes(['not-existing', 'other-type']));

        // Assert without input source.
        $notificationService = NotificationService::init()
            ->forBuilding($building);

        $notificationService->setType('nullable')->setActive([$uuid4]);
        // Check if type is active even without input source query (and without uuid).
        $this->assertTrue($notificationService->isActive());
        // Check for type test also (activated on line 66).
        $this->assertTrue($notificationService->setType('test')->isActive());

        $this->assertTrue($notificationService->hasActiveTypes(['nullable']));
        $this->assertTrue($notificationService->hasActiveTypes(['test']));
        $this->assertTrue($notificationService->hasActiveTypes(['className']));
        $this->assertTrue($notificationService->hasActiveTypes(['test', 'className']));
    }

    public function test_deactivating_clears_notification()
    {
        $this->seed(InputSourcesTableSeeder::class);
        $building = Building::factory()->withUser()->create();

        $residentInputSource = InputSource::resident();
        $masterInputSource = InputSource::master();

        $uuid1 = Str::uuid();
        $uuid2 = Str::uuid();

        $notificationService = NotificationService::init()
            ->forBuilding($building)
            ->forInputSource($residentInputSource)
            ->setType('test');

        $notificationService->setActive([$uuid1, $uuid2]);

        $notificationService->setUuid($uuid1)->deactivate();

        // Assert uuid1 has been deactivated.
        $this->assertDatabaseCount('notifications', 2);
        foreach ([$residentInputSource, $masterInputSource] as $inputSource) {
            $this->assertDatabaseHas('notifications', [
                'building_id' => $building->id,
                'input_source_id' => $inputSource->id,
                'uuid' => $uuid2,
                'type' => 'test',
            ]);
            $this->assertDatabaseMissing('notifications', [
                'building_id' => $building->id,
                'input_source_id' => $inputSource->id,
                'uuid' => $uuid1,
                'type' => 'test',
            ]);
        }
    }

    public function test_nullable_input_source_handles_as_expected()
    {
        $this->seed(InputSourcesTableSeeder::class);
        $building = Building::factory()->withUser()->create();

        $masterInputSource = InputSource::master();

        $uuid1 = Str::uuid();
        $uuid2 = Str::uuid();

        $notificationService = NotificationService::init()
            ->forBuilding($building)
            ->setType('test');

        $notificationService->setActive([$uuid1, $uuid2]);

        $this->assertDatabaseCount('notifications', 4);
        foreach ([$uuid1, $uuid2] as $uuid) {
            $this->assertDatabaseHas('notifications', [
                'building_id' => $building->id,
                'input_source_id' => null,
                'uuid' => $uuid,
                'type' => 'test',
            ]);
            $this->assertDatabaseHas('notifications', [
                'building_id' => $building->id,
                'input_source_id' => $masterInputSource->id,
                'uuid' => $uuid,
                'type' => 'test',
            ]);
        }

        // Ensure uuid deleted without input source.
        $notificationService->setUuid($uuid1)->deactivate();
        $this->assertDatabaseMissing('notifications', [
            'building_id' => $building->id,
            'input_source_id' => null,
            'uuid' => $uuid1,
            'type' => 'test',
        ]);
        $this->assertDatabaseMissing('notifications', [
            'building_id' => $building->id,
            'input_source_id' => $masterInputSource->id,
            'uuid' => $uuid1,
            'type' => 'test',
        ]);
        $this->assertDatabaseHas('notifications', [
            'building_id' => $building->id,
            'input_source_id' => null,
            'uuid' => $uuid2,
            'type' => 'test',
        ]);
        $this->assertDatabaseHas('notifications', [
            'building_id' => $building->id,
            'input_source_id' => $masterInputSource->id,
            'uuid' => $uuid2,
            'type' => 'test',
        ]);

        // Ensure we can find an active notification with and without input source
        $this->assertTrue($notificationService->setUuid($uuid2)->isActive());
        $this->assertTrue($notificationService->forInputSource($masterInputSource)->isActive());
    }
}