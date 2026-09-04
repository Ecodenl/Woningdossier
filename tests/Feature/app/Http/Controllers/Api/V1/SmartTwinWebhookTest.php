<?php

namespace Tests\Feature\app\Http\Controllers\Api\V1;

use App\Enums\SmartTwin\EventType;
use App\Events\SmartTwinCallbackReceived;
use App\Http\Middleware\Api\SmartTwinSigned;
use App\Models\Account;
use App\Models\Building;
use App\Models\BuildingCoachStatus;
use App\Models\Cooperation;
use App\Models\User;
use App\Services\SmartTwin\Api\UserRole;
use Database\Seeders\RoleTableSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

final class SmartTwinWebhookTest extends TestCase
{
    use RefreshDatabase;

    private const SMARTTWIN_USER_ID = '70f2d177-ceb0-420c-aaa3-3e3d50570154';

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleTableSeeder::class);
    }

    /**
     * A resident account with its own building, linked to SmartTwin.
     */
    private function makeResidentBuilding(array $address = []): Building
    {
        $account = Account::factory()->create([
            'smarttwin_user_id'   => self::SMARTTWIN_USER_ID,
            'smarttwin_user_role' => UserRole::Resident,
        ]);

        $user = User::factory()->asResident()->create([
            'account_id'     => $account->getKey(),
            'cooperation_id' => Cooperation::factory()->create()->getKey(),
        ]);

        return Building::factory()->create(array_merge([
            'user_id'     => $user->getKey(),
            'postal_code' => '5224 HD',
            'number'      => '1',
            'extension'   => '',
        ], $address));
    }

    private function postWebhook(array $data = []): TestResponse
    {
        return $this->withoutMiddleware(SmartTwinSigned::class)
            ->postJson(route('api.v1.smarttwin.store'), [
                'data' => array_merge([
                    'EventType'           => EventType::RESIDENT_SCAN_FINISHED->value,
                    'UserId'              => self::SMARTTWIN_USER_ID,
                    'DossierId'           => 'dossier-guid-1',
                    'PostalCode'          => '5224HD',
                    'HouseNumber'         => '1',
                    'HouseNumberAddition' => null,
                    'HouseLetter'         => null,
                ], $data),
            ]);
    }

    public function test_webhook_stores_callback_keyed_by_event_type(): void
    {
        $building = $this->makeResidentBuilding();

        $this->postWebhook()->assertNoContent();

        $callbacks = $building->fresh()->getSmartTwinCallbacks();

        $this->assertArrayHasKey(EventType::RESIDENT_SCAN_FINISHED->value, $callbacks);
        $this->assertSame(
            'dossier-guid-1',
            $callbacks[EventType::RESIDENT_SCAN_FINISHED->value]['DossierId'],
        );
    }

    public function test_webhook_is_latest_wins_and_holds_at_most_one_per_flow(): void
    {
        $building = $this->makeResidentBuilding();
        // The same account also coaches a building, so the coach flow resolves too.
        $coachBuilding = $this->giveAccountACoachedBuilding();

        $this->postWebhook()->assertNoContent();
        $this->postWebhook(['EventType' => EventType::COACH_SCAN_FINISHED->value])->assertNoContent();
        // Second resident webhook overwrites the first rather than appending.
        $this->postWebhook(['DossierId' => 'dossier-guid-2'])->assertNoContent();

        $callbacks = $building->fresh()->getSmartTwinCallbacks();

        $this->assertCount(1, $callbacks);
        $this->assertSame(
            'dossier-guid-2',
            $callbacks[EventType::RESIDENT_SCAN_FINISHED->value]['DossierId'],
        );

        // The coach flow landed on the coached building, not on the resident's own home.
        $this->assertArrayHasKey(
            EventType::COACH_SCAN_FINISHED->value,
            $coachBuilding->fresh()->getSmartTwinCallbacks(),
        );
    }

    public function test_webhook_ignores_unknown_event_type(): void
    {
        $building = $this->makeResidentBuilding();

        $this->postWebhook(['EventType' => 'totally.unknown.event'])->assertNoContent();

        $this->assertNull($building->fresh()->smarttwin_callback);
    }

    public function test_webhook_ignores_an_address_the_account_cannot_reach(): void
    {
        $building = $this->makeResidentBuilding();

        $this->postWebhook(['HouseNumber' => '2'])->assertNoContent();

        $this->assertNull($building->fresh()->smarttwin_callback);
    }

    public function test_webhook_ignores_an_unknown_smarttwin_user(): void
    {
        $building = $this->makeResidentBuilding();

        $this->postWebhook(['UserId' => 'nobody-we-know'])->assertNoContent();

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

    /**
     * Attach a second user to the account, in another cooperation and with the coach role, and give
     * it full access to a resident's building at the same address.
     */
    private function giveAccountACoachedBuilding(): Building
    {
        $account = Account::where('smarttwin_user_id', self::SMARTTWIN_USER_ID)->firstOrFail();

        $coach = User::factory()->asCoach()->create([
            'account_id'     => $account->getKey(),
            'cooperation_id' => Cooperation::factory()->create()->getKey(),
        ]);

        $resident = User::factory()->asResident()->create([
            'account_id'     => Account::factory()->create()->getKey(),
            'cooperation_id' => $coach->cooperation_id,
            'allow_access'   => true,
        ]);

        $building = Building::factory()->create([
            'user_id'     => $resident->getKey(),
            'postal_code' => '5224 HD',
            'number'      => '1',
            'extension'   => '',
        ]);

        $building->buildingCoachStatuses()->create([
            'coach_id' => $coach->getKey(),
            'status'   => BuildingCoachStatus::STATUS_ADDED,
        ]);
        $building->buildingPermissions()->create(['user_id' => $coach->getKey()]);

        return $building;
    }
}
