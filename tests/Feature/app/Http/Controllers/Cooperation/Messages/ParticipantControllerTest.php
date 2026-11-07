<?php

namespace Tests\Feature\app\Http\Controllers\Cooperation\Messages;

use App\Helpers\HoomdossierSession;
use App\Helpers\RoleHelper;
use App\Models\Building;
use App\Models\BuildingCoachStatus;
use App\Models\BuildingPermission;
use App\Models\Cooperation;
use App\Models\InputSource;
use App\Models\PrivateMessage;
use App\Models\Role;
use App\Models\User;
use App\Services\BuildingCoachStatusService;
use App\Services\BuildingPermissionService;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

/**
 * Attaching and detaching coaches, and marking a conversation read. Each asks who is asking: the
 * routes sit behind nothing but a login.
 */
final class ParticipantControllerTest extends TestCase
{
    use RefreshDatabase;

    public bool $seed = true;
    public string $seeder = DatabaseSeeder::class;

    private Cooperation $cooperation;
    private User $resident;
    private Building $building;
    private User $coach;

    protected function setUp(): void
    {
        parent::setUp();

        Mail::fake();

        $this->cooperation = Cooperation::factory()->create();
        HoomdossierSession::setCooperation($this->cooperation);

        $this->resident = User::factory()
            ->withAccount()
            ->asResident()
            ->create(['cooperation_id' => $this->cooperation->id, 'allow_access' => true]);
        $this->building = Building::factory()->create(['user_id' => $this->resident->id]);

        // With a building of their own, like every user has.
        $this->coach = $this->makeUser('asCoach');
        $this->attach($this->coach, $this->building);
    }

    private function makeUser(string $role): User
    {
        return User::factory()
            ->withAccount()
            ->withBuilding()
            ->{$role}()
            ->create(['cooperation_id' => $this->cooperation->id]);
    }

    private function attach(User $coach, Building $building): void
    {
        BuildingPermissionService::givePermission($coach, $building);
        BuildingCoachStatusService::giveAccess($coach, $building);
    }

    private function actAs(User $user, string $role, string $inputSourceShort): void
    {
        $inputSource = InputSource::findByShort($inputSourceShort);

        $this->actingAs($user->account);
        HoomdossierSession::setHoomdossierSessions(
            $user->building ?? $this->building,
            $inputSource,
            $inputSource,
            Role::findByName($role),
        );
    }

    private function actAsResident(): void
    {
        $this->actAs($this->resident, RoleHelper::ROLE_RESIDENT, InputSource::RESIDENT_SHORT);
    }

    private function actAsCoordinator(): void
    {
        $this->actAs($this->makeUser('asCoordinator'), RoleHelper::ROLE_COORDINATOR, InputSource::COOPERATION_SHORT);
    }

    private function revoke(User $coach, Building $building): TestResponse
    {
        return $this->post(route('cooperation.messages.participants.revoke-access', ['cooperation' => $this->cooperation]), [
            'user_id' => $coach->id,
            'building_owner_id' => $building->id,
        ]);
    }

    private function add(User $coach, Building $building): TestResponse
    {
        return $this->post(route('cooperation.messages.participants.add-with-building-access', ['cooperation' => $this->cooperation]), [
            'user_id' => $coach->id,
            'building_id' => $building->id,
        ]);
    }

    private function isAttached(User $coach, Building $building): bool
    {
        return BuildingCoachStatusService::getConnectedCoachesByBuilding($building)->contains('coach_id', $coach->id);
    }

    private function statusCount(User $coach, Building $building): int
    {
        return BuildingCoachStatus::where('coach_id', $coach->id)->where('building_id', $building->id)->count();
    }

    public function test_a_resident_detaches_a_coach_from_their_own_building(): void
    {
        $this->actAsResident();

        $this->revoke($this->coach, $this->building)->assertRedirect();

        $this->assertFalse($this->isAttached($this->coach, $this->building));
        $this->assertDatabaseMissing('building_permissions', [
            'user_id' => $this->coach->id,
            'building_id' => $this->building->id,
        ]);
    }

    public function test_a_coordinator_detaches_a_coach(): void
    {
        $this->actAsCoordinator();

        $this->revoke($this->coach, $this->building)->assertRedirect();

        $this->assertFalse($this->isAttached($this->coach, $this->building));
    }

    public function test_a_resident_cannot_detach_a_coach_from_someone_elses_building(): void
    {
        $neighbour = $this->makeUser('asResident');
        $this->actAs($neighbour, RoleHelper::ROLE_RESIDENT, InputSource::RESIDENT_SHORT);

        $this->revoke($this->coach, $this->building)->assertForbidden();

        $this->assertTrue($this->isAttached($this->coach, $this->building));
    }

    public function test_a_coach_cannot_detach_another_coach(): void
    {
        $otherCoach = $this->makeUser('asCoach');
        $this->attach($otherCoach, $this->building);
        $this->actAs($otherCoach, RoleHelper::ROLE_COACH, InputSource::COACH_SHORT);

        $this->revoke($this->coach, $this->building)->assertForbidden();

        $this->assertTrue($this->isAttached($this->coach, $this->building));
    }

    public function test_detaching_a_coach_that_is_not_attached_writes_nothing(): void
    {
        $strangerCoach = $this->makeUser('asCoach');
        $this->actAsResident();

        $this->revoke($strangerCoach, $this->building)->assertForbidden();

        // A stray "removed" would cancel out the next "added", so attaching later would not stick.
        $this->assertSame(0, $this->statusCount($strangerCoach, $this->building));
    }

    public function test_a_coordinator_cannot_detach_a_coach_in_another_cooperation(): void
    {
        $otherCooperation = Cooperation::factory()->create();
        $otherResident = User::factory()->withAccount()->asResident()->create(['cooperation_id' => $otherCooperation->id]);
        $otherBuilding = Building::factory()->create(['user_id' => $otherResident->id]);
        $otherCoach = User::factory()->withAccount()->withBuilding()->asCoach()->create(['cooperation_id' => $otherCooperation->id]);
        $this->attach($otherCoach, $otherBuilding);

        $this->actAsCoordinator();

        // Not the 404 a browser would get: the cooperation scope that hides the coach is off in tests,
        // so this is the policy's own check speaking.
        $this->revoke($otherCoach, $otherBuilding)->assertForbidden();

        $this->assertTrue($this->isAttached($otherCoach, $otherBuilding));
    }

    public function test_a_coordinator_cannot_attach_a_coach_in_another_cooperation(): void
    {
        $otherCooperation = Cooperation::factory()->create();
        $otherResident = User::factory()->withAccount()->asResident()->create(['cooperation_id' => $otherCooperation->id]);
        $otherBuilding = Building::factory()->create(['user_id' => $otherResident->id]);
        $newCoach = $this->makeUser('asCoach');

        $this->actAsCoordinator();

        $this->add($newCoach, $otherBuilding)->assertNotFound();

        $this->assertSame(0, $this->statusCount($newCoach, $otherBuilding));
    }

    public function test_a_coordinator_attaches_a_coach(): void
    {
        $newCoach = $this->makeUser('asCoach');
        $this->actAsCoordinator();

        $this->add($newCoach, $this->building)->assertRedirect();

        $this->assertTrue($this->isAttached($newCoach, $this->building));
        $this->assertDatabaseHas('building_permissions', [
            'user_id' => $newCoach->id,
            'building_id' => $this->building->id,
        ]);
    }

    public function test_attaching_an_attached_coach_again_changes_nothing(): void
    {
        $this->actAsCoordinator();

        $this->add($this->coach, $this->building)->assertRedirect();

        // One "added", so one revoke still detaches them.
        $this->assertSame(1, $this->statusCount($this->coach, $this->building));
        $this->assertSame(1, BuildingPermission::where('user_id', $this->coach->id)->where('building_id', $this->building->id)->count());
        Mail::assertNothingQueued();
    }

    public function test_a_resident_cannot_attach_a_coach(): void
    {
        $newCoach = $this->makeUser('asCoach');
        $this->actAsResident();

        $this->add($newCoach, $this->building)->assertForbidden();

        $this->assertFalse($this->isAttached($newCoach, $this->building));
        Mail::assertNothingQueued();
    }

    public function test_only_a_coach_can_be_attached(): void
    {
        $neighbour = $this->makeUser('asResident');
        $this->actAsCoordinator();

        $this->add($neighbour, $this->building)->assertNotFound();

        $this->assertSame(0, $this->statusCount($neighbour, $this->building));
    }

    public function test_a_coordinator_marks_a_conversation_read(): void
    {
        PrivateMessage::createQuietly([
            'building_id' => $this->building->id,
            'from_user_id' => $this->resident->id,
            'from_user' => $this->resident->getFullName(),
            'message' => 'Een eerste bericht',
            'is_public' => true,
            'to_cooperation_id' => $this->cooperation->id,
        ]);
        $this->actAsCoordinator();

        $this->post(route('cooperation.messages.participants.set-read', ['cooperation' => $this->cooperation]), [
            'is_public' => true,
            'building_id' => $this->building->id,
        ])->assertOk()->assertJson(['unread' => 0]);
    }

    public function test_a_resident_cannot_mark_someone_elses_conversation_read(): void
    {
        $neighbour = $this->makeUser('asResident');
        $this->actAs($neighbour, RoleHelper::ROLE_RESIDENT, InputSource::RESIDENT_SHORT);

        $this->post(route('cooperation.messages.participants.set-read', ['cooperation' => $this->cooperation]), [
            'is_public' => true,
            'building_id' => $this->building->id,
        ])->assertForbidden();
    }
}
