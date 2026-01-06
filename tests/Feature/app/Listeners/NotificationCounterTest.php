<?php

namespace Tests\Feature\app\Listeners;

use App\Events\ParticipantAddedEvent;
use App\Events\ParticipantRevokedEvent;
use App\Events\UserAllowedAccessToHisBuilding;
use App\Events\UserRevokedAccessToHisBuilding;
use App\Helpers\HoomdossierSession;
use App\Helpers\RoleHelper;
use App\Models\Account;
use App\Models\Building;
use App\Models\Cooperation;
use App\Models\InputSource;
use App\Models\PrivateMessage;
use App\Models\PrivateMessageView;
use App\Models\Role;
use App\Models\User;
use App\Services\BuildingCoachStatusService;
use App\Services\BuildingPermissionService;
use App\Services\PrivateMessageService;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

/**
 * Tests for the notification counter (unread messages badge) functionality.
 *
 * This test class verifies that PrivateMessages and PrivateMessageViews are created correctly
 * for different scenarios, and that the notification counter returns the expected counts
 * for each role (Bewoner, Coach, Coordinator, Cooperatie Admin).
 *
 * @see /notification-counter.md for the complete matrix overview
 */
final class NotificationCounterTest extends TestCase
{
    use RefreshDatabase;

    public bool $seed = true;
    public string $seeder = DatabaseSeeder::class;

    private Cooperation $cooperation;
    private InputSource $residentInputSource;
    private InputSource $coachInputSource;
    private InputSource $cooperationInputSource;
    private Role $residentRole;
    private Role $coachRole;
    private Role $coordinatorRole;
    private Role $cooperationAdminRole;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cooperation = Cooperation::factory()->create();

        $this->residentInputSource = InputSource::findByShort(InputSource::RESIDENT_SHORT);
        $this->coachInputSource = InputSource::findByShort(InputSource::COACH_SHORT);
        $this->cooperationInputSource = InputSource::findByShort(InputSource::COOPERATION_SHORT);

        $this->residentRole = Role::findByName(RoleHelper::ROLE_RESIDENT);
        $this->coachRole = Role::findByName(RoleHelper::ROLE_COACH);
        $this->coordinatorRole = Role::findByName(RoleHelper::ROLE_COORDINATOR);
        $this->cooperationAdminRole = Role::findByName(RoleHelper::ROLE_COOPERATION_ADMIN);
    }

    private function createResident(): User
    {
        $resident = User::factory()
            ->withAccount()
            ->asResident()
            ->create([
                'cooperation_id' => $this->cooperation->id,
                'allow_access' => true,
            ]);
        Building::factory()->create(['user_id' => $resident->id]);

        return $resident;
    }

    private function createCoach(): User
    {
        $coach = User::factory()
            ->withAccount()
            ->asCoach()
            ->create([
                'cooperation_id' => $this->cooperation->id,
            ]);
        Building::factory()->create(['user_id' => $coach->id]);

        return $coach;
    }

    private function createCoordinator(): User
    {
        $coordinator = User::factory()
            ->withAccount()
            ->asCoordinator()
            ->create([
                'cooperation_id' => $this->cooperation->id,
            ]);
        Building::factory()->create(['user_id' => $coordinator->id]);

        return $coordinator;
    }

    private function createCooperationAdmin(): User
    {
        $admin = User::factory()
            ->withAccount()
            ->asCooperationAdmin()
            ->create([
                'cooperation_id' => $this->cooperation->id,
            ]);
        Building::factory()->create(['user_id' => $admin->id]);

        return $admin;
    }

    private function setSessionForUser(User $user, Role $role): void
    {
        $this->actingAs($user->account);
        HoomdossierSession::setCooperation($this->cooperation);
        HoomdossierSession::setBuilding($user->building);
        HoomdossierSession::setRole($role);
        HoomdossierSession::setInputSource($role->inputSource);
        HoomdossierSession::setInputSourceValue($role->inputSource);
    }

    private function getUnreadCountForResident(User $resident): int
    {
        $this->setSessionForUser($resident, $this->residentRole);

        return PrivateMessageView::getTotalUnreadMessagesForCurrentRole();
    }

    private function getUnreadCountForCoach(User $coach): int
    {
        $this->setSessionForUser($coach, $this->coachRole);

        return PrivateMessageView::getTotalUnreadMessagesForCurrentRole();
    }

    private function getUnreadCountForCoordinator(User $coordinator): int
    {
        $this->setSessionForUser($coordinator, $this->coordinatorRole);

        return PrivateMessageView::getTotalUnreadMessagesForCurrentRole();
    }

    private function getUnreadCountForCooperationAdmin(User $admin): int
    {
        $this->setSessionForUser($admin, $this->cooperationAdminRole);

        return PrivateMessageView::getTotalUnreadMessagesForCurrentRole();
    }

    // =========================================================================
    // Scenario 1: Account aanmaken ZONDER coach koppeling
    // =========================================================================

    public function testAccountCreatedWithoutCoach_ResidentReceivesWelcomeMessage(): void
    {
        $resident = $this->createResident();
        $building = $resident->building;

        // Simulate the UserAllowedAccessToHisBuilding event (normally fired during account creation)
        $this->setSessionForUser($resident, $this->residentRole);
        UserAllowedAccessToHisBuilding::dispatch($resident, $building);

        // Check that welcome message was created
        $this->assertDatabaseHas('private_messages', [
            'building_id' => $building->id,
            'from_cooperation_id' => $this->cooperation->id,
            'is_public' => true,
        ]);

        // Check notification counts
        $this->assertEquals(1, $this->getUnreadCountForResident($resident));

        // Coordinator and admin should NOT have unread messages for this
        $coordinator = $this->createCoordinator();
        $this->assertEquals(0, $this->getUnreadCountForCoordinator($coordinator));

        $admin = $this->createCooperationAdmin();
        $this->assertEquals(0, $this->getUnreadCountForCooperationAdmin($admin));
    }

    // =========================================================================
    // Scenario 2: Account aanmaken MET coach koppeling
    // =========================================================================

    public function testAccountCreatedWithCoach_ResidentAndCoachReceiveMessages(): void
    {
        $resident = $this->createResident();
        $building = $resident->building;
        $coach = $this->createCoach();
        $coordinator = $this->createCoordinator();

        // Set session as coordinator (who creates the account)
        $this->setSessionForUser($coordinator, $this->coordinatorRole);

        // 1. Welcome message via UserAllowedAccessToHisBuilding
        UserAllowedAccessToHisBuilding::dispatch($resident, $building);

        // 2. Coach gets linked via ParticipantAddedEvent
        BuildingPermissionService::givePermission($coach, $building);
        BuildingCoachStatusService::giveAccess($coach, $building);
        ParticipantAddedEvent::dispatch($coach, $building, $coordinator->account, $this->cooperation);

        // Check messages exist
        $this->assertDatabaseHas('private_messages', [
            'building_id' => $building->id,
            'from_user_id' => $coach->id,
            'is_public' => true,
        ]);

        // Check notification counts
        // Resident: +1 (welcome) + 1 (coach added) = 2
        $this->assertEquals(2, $this->getUnreadCountForResident($resident));

        // Coach: +1 (coach added message)
        $this->assertEquals(1, $this->getUnreadCountForCoach($coach));

        // Coordinator: 0 (created both messages, excluded as sender)
        $this->assertEquals(0, $this->getUnreadCountForCoordinator($coordinator));
    }

    // =========================================================================
    // Scenario 3: Coach koppelen aan bestaande woning
    // =========================================================================

    public function testCoachLinkedToExistingBuilding_AllPartiesReceiveNotification(): void
    {
        $resident = $this->createResident();
        $building = $resident->building;
        $coach = $this->createCoach();
        $coordinator = $this->createCoordinator();

        // Create existing conversation
        $this->setSessionForUser($coordinator, $this->coordinatorRole);
        PrivateMessage::withoutEvents(function () use ($building) {
            return PrivateMessage::create([
                'is_public' => true,
                'from_cooperation_id' => $this->cooperation->id,
                'to_cooperation_id' => $this->cooperation->id,
                'from_user' => $this->cooperation->name,
                'message' => 'Existing conversation',
                'building_id' => $building->id,
            ]);
        });

        // Link coach (as coordinator)
        BuildingPermissionService::givePermission($coach, $building);
        BuildingCoachStatusService::giveAccess($coach, $building);
        ParticipantAddedEvent::dispatch($coach, $building, $coordinator->account, $this->cooperation);

        // Check notification counts
        // Resident: +1 (coach added)
        $this->assertEquals(1, $this->getUnreadCountForResident($resident));

        // Coach: +1 (coach added - the new coach receives notification about being added)
        $this->assertEquals(1, $this->getUnreadCountForCoach($coach));

        // Coordinator: 0 (performed the action)
        $this->assertEquals(0, $this->getUnreadCountForCoordinator($coordinator));
    }

    public function testSecondCoachLinked_BothCoachesReceiveNotification(): void
    {
        $resident = $this->createResident();
        $building = $resident->building;
        $coach1 = $this->createCoach();
        $coach2 = $this->createCoach();
        $coordinator = $this->createCoordinator();

        $this->setSessionForUser($coordinator, $this->coordinatorRole);

        // Link first coach
        BuildingPermissionService::givePermission($coach1, $building);
        BuildingCoachStatusService::giveAccess($coach1, $building);
        ParticipantAddedEvent::dispatch($coach1, $building, $coordinator->account, $this->cooperation);

        // Mark all as read for clean slate
        PrivateMessageView::query()->update(['read_at' => now()]);

        // Link second coach
        BuildingPermissionService::givePermission($coach2, $building);
        BuildingCoachStatusService::giveAccess($coach2, $building);
        ParticipantAddedEvent::dispatch($coach2, $building, $coordinator->account, $this->cooperation);

        // Resident: +1
        $this->assertEquals(1, $this->getUnreadCountForResident($resident));

        // Coach 1 (existing): +1 (notification about coach2 being added)
        $this->assertEquals(1, $this->getUnreadCountForCoach($coach1));

        // Coach 2 (new): +1 (notification about being added)
        $this->assertEquals(1, $this->getUnreadCountForCoach($coach2));

        // Coordinator: 0
        $this->assertEquals(0, $this->getUnreadCountForCoordinator($coordinator));
    }

    // =========================================================================
    // Scenario 4: Coach ontkoppelen door coordinator/admin
    // =========================================================================

    public function testCoachUnlinkedByCoordinator_RemovedCoachGetsNoNotification(): void
    {
        $resident = $this->createResident();
        $building = $resident->building;
        $coach = $this->createCoach();
        $coordinator = $this->createCoordinator();

        $this->setSessionForUser($coordinator, $this->coordinatorRole);

        // Link coach first
        BuildingPermissionService::givePermission($coach, $building);
        BuildingCoachStatusService::giveAccess($coach, $building);
        ParticipantAddedEvent::dispatch($coach, $building, $coordinator->account, $this->cooperation);

        // Mark all as read for clean slate
        PrivateMessageView::query()->update(['read_at' => now()]);

        // Unlink coach (coach is unlinked BEFORE event is dispatched)
        BuildingPermissionService::revokePermission($coach, $building);
        BuildingCoachStatusService::revokeAccess($coach, $building);
        ParticipantRevokedEvent::dispatch($coach, $building);

        // Resident: +1 (coach removed message)
        $this->assertEquals(1, $this->getUnreadCountForResident($resident));

        // Removed coach: 0 (no longer in group participants when message is created)
        $this->assertEquals(0, $this->getUnreadCountForCoach($coach));

        // Coordinator: 0 (performed the action)
        $this->assertEquals(0, $this->getUnreadCountForCoordinator($coordinator));
    }

    public function testCoachUnlinked_OtherCoachesStillReceiveNotification(): void
    {
        $resident = $this->createResident();
        $building = $resident->building;
        $coach1 = $this->createCoach();
        $coach2 = $this->createCoach();
        $coordinator = $this->createCoordinator();

        $this->setSessionForUser($coordinator, $this->coordinatorRole);

        // Link both coaches
        BuildingPermissionService::givePermission($coach1, $building);
        BuildingCoachStatusService::giveAccess($coach1, $building);
        BuildingPermissionService::givePermission($coach2, $building);
        BuildingCoachStatusService::giveAccess($coach2, $building);

        // Mark all as read for clean slate
        PrivateMessageView::query()->update(['read_at' => now()]);

        // Unlink coach1
        BuildingPermissionService::revokePermission($coach1, $building);
        BuildingCoachStatusService::revokeAccess($coach1, $building);
        ParticipantRevokedEvent::dispatch($coach1, $building);

        // Resident: +1
        $this->assertEquals(1, $this->getUnreadCountForResident($resident));

        // Coach 1 (removed): 0
        $this->assertEquals(0, $this->getUnreadCountForCoach($coach1));

        // Coach 2 (still linked): +1
        $this->assertEquals(1, $this->getUnreadCountForCoach($coach2));

        // Coordinator: 0
        $this->assertEquals(0, $this->getUnreadCountForCoordinator($coordinator));
    }

    // =========================================================================
    // Scenario 5: Bewoner stuurt bericht
    // =========================================================================

    public function testResidentSendsMessage_CooperationAndCoachesReceiveNotification(): void
    {
        $resident = $this->createResident();
        $building = $resident->building;
        $coach = $this->createCoach();
        $coordinator = $this->createCoordinator();

        // Setup: Link coach to building
        $this->setSessionForUser($coordinator, $this->coordinatorRole);
        BuildingPermissionService::givePermission($coach, $building);
        BuildingCoachStatusService::giveAccess($coach, $building);

        // Mark all as read for clean slate
        PrivateMessageView::query()->update(['read_at' => now()]);

        // Resident sends message
        $this->setSessionForUser($resident, $this->residentRole);
        $request = new Request([
            'message' => 'Hello from resident',
            'is_public' => true,
            'building_id' => $building->id,
        ]);
        PrivateMessageService::create($request);

        // Resident: 0 (sender)
        $this->assertEquals(0, $this->getUnreadCountForResident($resident));

        // Coach: +1
        $this->assertEquals(1, $this->getUnreadCountForCoach($coach));

        // Coordinator: +1 (via to_cooperation_id)
        $this->assertEquals(1, $this->getUnreadCountForCoordinator($coordinator));

        // Admin: +1 (same to_cooperation_id record)
        $admin = $this->createCooperationAdmin();
        $this->assertEquals(1, $this->getUnreadCountForCooperationAdmin($admin));
    }

    // =========================================================================
    // Scenario 6: Cooperatie stuurt bericht
    // =========================================================================

    public function testCoordinatorSendsMessage_ResidentAndCoachesReceiveNotification(): void
    {
        $resident = $this->createResident();
        $building = $resident->building;
        $coach = $this->createCoach();
        $coordinator = $this->createCoordinator();

        // Setup: Link coach to building
        $this->setSessionForUser($coordinator, $this->coordinatorRole);
        BuildingPermissionService::givePermission($coach, $building);
        BuildingCoachStatusService::giveAccess($coach, $building);

        // Mark all as read for clean slate
        PrivateMessageView::query()->update(['read_at' => now()]);

        // Coordinator sends message
        $request = new Request([
            'message' => 'Hello from cooperation',
            'is_public' => true,
            'building_id' => $building->id,
        ]);
        PrivateMessageService::create($request);

        // Resident: +1
        $this->assertEquals(1, $this->getUnreadCountForResident($resident));

        // Coach: +1
        $this->assertEquals(1, $this->getUnreadCountForCoach($coach));

        // Coordinator: 0 (sender, speaks on behalf of cooperation)
        $this->assertEquals(0, $this->getUnreadCountForCoordinator($coordinator));

        // Admin: 0 (cooperation is sender)
        $admin = $this->createCooperationAdmin();
        $this->assertEquals(0, $this->getUnreadCountForCooperationAdmin($admin));
    }

    // =========================================================================
    // Scenario 7: Coach stuurt privebericht
    // =========================================================================

    public function testCoachSendsPrivateMessage_ResidentExcluded_CooperationReceives(): void
    {
        $resident = $this->createResident();
        $building = $resident->building;
        $coach = $this->createCoach();
        $coordinator = $this->createCoordinator();

        // Setup: Link coach to building
        $this->setSessionForUser($coordinator, $this->coordinatorRole);
        BuildingPermissionService::givePermission($coach, $building);
        BuildingCoachStatusService::giveAccess($coach, $building);

        // Mark all as read for clean slate
        PrivateMessageView::query()->update(['read_at' => now()]);

        // Coach sends private message
        $this->setSessionForUser($coach, $this->coachRole);
        $request = new Request([
            'message' => 'Private message from coach',
            'is_public' => false,
            'building_id' => $building->id,
        ]);
        PrivateMessageService::create($request);

        // Resident: 0 (explicitly excluded from private messages)
        $this->assertEquals(0, $this->getUnreadCountForResident($resident));

        // Coach: 0 (sender)
        $this->assertEquals(0, $this->getUnreadCountForCoach($coach));

        // Coordinator: +1 (via to_cooperation_id, coach is not coordinator/admin)
        $this->assertEquals(1, $this->getUnreadCountForCoordinator($coordinator));

        // Admin: +1 (same to_cooperation_id record)
        $admin = $this->createCooperationAdmin();
        $this->assertEquals(1, $this->getUnreadCountForCooperationAdmin($admin));
    }

    public function testCoachSendsPrivateMessage_OtherCoachesReceive(): void
    {
        $resident = $this->createResident();
        $building = $resident->building;
        $coach1 = $this->createCoach();
        $coach2 = $this->createCoach();
        $coordinator = $this->createCoordinator();

        // Setup: Link both coaches
        $this->setSessionForUser($coordinator, $this->coordinatorRole);
        BuildingPermissionService::givePermission($coach1, $building);
        BuildingCoachStatusService::giveAccess($coach1, $building);
        BuildingPermissionService::givePermission($coach2, $building);
        BuildingCoachStatusService::giveAccess($coach2, $building);

        // Mark all as read for clean slate
        PrivateMessageView::query()->update(['read_at' => now()]);

        // Coach1 sends private message
        $this->setSessionForUser($coach1, $this->coachRole);
        $request = new Request([
            'message' => 'Private message from coach1',
            'is_public' => false,
            'building_id' => $building->id,
        ]);
        PrivateMessageService::create($request);

        // Resident: 0 (excluded from private messages)
        $this->assertEquals(0, $this->getUnreadCountForResident($resident));

        // Coach1: 0 (sender)
        $this->assertEquals(0, $this->getUnreadCountForCoach($coach1));

        // Coach2: +1 (other coaches receive private messages)
        $this->assertEquals(1, $this->getUnreadCountForCoach($coach2));
    }

    // =========================================================================
    // Scenario 8: Coordinator/Admin stuurt privebericht
    // =========================================================================

    public function testCoordinatorSendsPrivateMessage_CoachesReceive_ResidentExcluded(): void
    {
        $resident = $this->createResident();
        $building = $resident->building;
        $coach = $this->createCoach();
        $coordinator = $this->createCoordinator();

        // Setup: Link coach
        $this->setSessionForUser($coordinator, $this->coordinatorRole);
        BuildingPermissionService::givePermission($coach, $building);
        BuildingCoachStatusService::giveAccess($coach, $building);

        // Mark all as read for clean slate
        PrivateMessageView::query()->update(['read_at' => now()]);

        // Coordinator sends private message
        $request = new Request([
            'message' => 'Private message from coordinator',
            'is_public' => false,
            'building_id' => $building->id,
        ]);
        PrivateMessageService::create($request);

        // Resident: 0 (excluded from private messages)
        $this->assertEquals(0, $this->getUnreadCountForResident($resident));

        // Coach: +1
        $this->assertEquals(1, $this->getUnreadCountForCoach($coach));

        // Coordinator: 0 (sender)
        $this->assertEquals(0, $this->getUnreadCountForCoordinator($coordinator));

        // Admin: 0 (cooperation is sender, no to_cooperation_id view created)
        $admin = $this->createCooperationAdmin();
        $this->assertEquals(0, $this->getUnreadCountForCooperationAdmin($admin));
    }

    // =========================================================================
    // Scenario 9: Bewoner verwijdert coach
    // =========================================================================

    public function testResidentRemovesCoach_CooperationReceivesNotification(): void
    {
        $resident = $this->createResident();
        $building = $resident->building;
        $coach = $this->createCoach();
        $coordinator = $this->createCoordinator();

        // Setup: Link coach
        $this->setSessionForUser($coordinator, $this->coordinatorRole);
        BuildingPermissionService::givePermission($coach, $building);
        BuildingCoachStatusService::giveAccess($coach, $building);

        // Mark all as read for clean slate
        PrivateMessageView::query()->update(['read_at' => now()]);

        // Resident removes coach (session is resident, not coordinator!)
        $this->setSessionForUser($resident, $this->residentRole);
        BuildingPermissionService::revokePermission($coach, $building);
        BuildingCoachStatusService::revokeAccess($coach, $building);
        ParticipantRevokedEvent::dispatch($coach, $building);

        // Resident: 0 (performed the action)
        $this->assertEquals(0, $this->getUnreadCountForResident($resident));

        // Removed coach: 0 (no longer in group)
        $this->assertEquals(0, $this->getUnreadCountForCoach($coach));

        // Coordinator: +1 (because resident, not coordinator, performed the action)
        $this->assertEquals(1, $this->getUnreadCountForCoordinator($coordinator));

        // Admin: +1 (same to_cooperation_id record)
        $admin = $this->createCooperationAdmin();
        $this->assertEquals(1, $this->getUnreadCountForCooperationAdmin($admin));
    }

    public function testResidentRemovesCoach_OtherCoachesReceiveNotification(): void
    {
        $resident = $this->createResident();
        $building = $resident->building;
        $coach1 = $this->createCoach();
        $coach2 = $this->createCoach();
        $coordinator = $this->createCoordinator();

        // Setup: Link both coaches
        $this->setSessionForUser($coordinator, $this->coordinatorRole);
        BuildingPermissionService::givePermission($coach1, $building);
        BuildingCoachStatusService::giveAccess($coach1, $building);
        BuildingPermissionService::givePermission($coach2, $building);
        BuildingCoachStatusService::giveAccess($coach2, $building);

        // Mark all as read for clean slate
        PrivateMessageView::query()->update(['read_at' => now()]);

        // Resident removes coach1
        $this->setSessionForUser($resident, $this->residentRole);
        BuildingPermissionService::revokePermission($coach1, $building);
        BuildingCoachStatusService::revokeAccess($coach1, $building);
        ParticipantRevokedEvent::dispatch($coach1, $building);

        // Resident: 0 (performed the action)
        $this->assertEquals(0, $this->getUnreadCountForResident($resident));

        // Coach1 (removed): 0
        $this->assertEquals(0, $this->getUnreadCountForCoach($coach1));

        // Coach2 (still linked): +1
        $this->assertEquals(1, $this->getUnreadCountForCoach($coach2));

        // Coordinator: +1 (resident performed action)
        $this->assertEquals(1, $this->getUnreadCountForCoordinator($coordinator));
    }

    // =========================================================================
    // Edge case: Coordinator vs Resident removes coach difference
    // =========================================================================

    public function testDifferenceBetweenCoordinatorAndResidentRemovingCoach(): void
    {
        // Scenario A: Coordinator removes coach
        $resident1 = $this->createResident();
        $building1 = $resident1->building;
        $coach1 = $this->createCoach();
        $coordinator = $this->createCoordinator();

        $this->setSessionForUser($coordinator, $this->coordinatorRole);
        BuildingPermissionService::givePermission($coach1, $building1);
        BuildingCoachStatusService::giveAccess($coach1, $building1);

        PrivateMessageView::query()->update(['read_at' => now()]);

        // Coordinator removes coach
        BuildingPermissionService::revokePermission($coach1, $building1);
        BuildingCoachStatusService::revokeAccess($coach1, $building1);
        ParticipantRevokedEvent::dispatch($coach1, $building1);

        $coordinatorCountAfterCoordinatorRemoves = $this->getUnreadCountForCoordinator($coordinator);

        // Scenario B: Resident removes coach (new building/users)
        $resident2 = $this->createResident();
        $building2 = $resident2->building;
        $coach2 = $this->createCoach();

        $this->setSessionForUser($coordinator, $this->coordinatorRole);
        BuildingPermissionService::givePermission($coach2, $building2);
        BuildingCoachStatusService::giveAccess($coach2, $building2);

        PrivateMessageView::query()->update(['read_at' => now()]);

        // Resident removes coach
        $this->setSessionForUser($resident2, $this->residentRole);
        BuildingPermissionService::revokePermission($coach2, $building2);
        BuildingCoachStatusService::revokeAccess($coach2, $building2);
        ParticipantRevokedEvent::dispatch($coach2, $building2);

        $coordinatorCountAfterResidentRemoves = $this->getUnreadCountForCoordinator($coordinator);

        // When coordinator removes: coordinator gets no notification (is sender)
        $this->assertEquals(0, $coordinatorCountAfterCoordinatorRemoves);

        // When resident removes: coordinator DOES get notification
        $this->assertEquals(1, $coordinatorCountAfterResidentRemoves);
    }
}
