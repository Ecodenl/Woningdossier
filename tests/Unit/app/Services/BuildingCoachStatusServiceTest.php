<?php

namespace Tests\Unit\app\Services;

use Database\Seeders\StatusesTableSeeder;
use App\Models\Account;
use App\Models\Building;
use App\Models\Cooperation;
use App\Models\User;
use App\Services\BuildingCoachStatusService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class BuildingCoachStatusServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(StatusesTableSeeder::class);
    }

    public function testGetConnectedBuildingsByUser(): void
    {
        $cooperation = Cooperation::factory()->create();

        $accounts = Account::factory()->count(5)->create()->each(function (Account $account) use ($cooperation) {
            $residentUser = User::factory()->create([
                'cooperation_id' => $cooperation->id,
                'account_id' => $account->id,
            ]);
            Building::factory()->create(['user_id' => $residentUser->id]);
        });

        $cooperation = Cooperation::factory()->create();
        $account = Account::factory()->create();
        $coachUser = User::factory()->create([
            'cooperation_id' => $cooperation->id,
            'account_id' => $account->id,
        ]);
        Building::factory()->create(['user_id' => $coachUser->id]);

        /** @var Account $account */
        foreach ($accounts as $i => $account) {
            $user = $account->users()->first();
            // give the coach access to the resident his building
            BuildingCoachStatusService::giveAccess($coachUser, $user->building);

            $user = $account->users()->first();
            // now revoke the coach access
            BuildingCoachStatusService::revokeAccess($coachUser, $user->building);

            // and only give access for 3 users.
            if ($i < 3) {
                $user = $account->users()->first();
                // give the coach access to the resident his building
                BuildingCoachStatusService::giveAccess($coachUser, $user->building);
            }
        }

        $connectedBuildingsForCoach = BuildingCoachStatusService::getConnectedBuildingsByUser($coachUser);

        $this->assertCount(3, $connectedBuildingsForCoach);
    }

    public function testGetConnectedCoachesByBuildingId(): void
    {
        $cooperation = Cooperation::factory()->create();
        $account = Account::factory()->create();
        $residentUser = User::factory()->create([
            'cooperation_id' => $cooperation->id,
            'account_id' => $account->id,
        ]);
        Building::factory()->create(['user_id' => $residentUser->id]);

        $i = 0;
        Account::factory()->count(5)->create()->each(function (Account $account) use ($cooperation, $residentUser, &$i) {
            $coachUser = User::factory()->create([
                'cooperation_id' => $cooperation->id,
                'account_id' => $account->id,
            ]);
            Building::factory()->create(['user_id' => $coachUser->id]);

            // give the coach access to the resident his building
            BuildingCoachStatusService::giveAccess($coachUser, $residentUser->building);

            // now revoke the coach access
            BuildingCoachStatusService::revokeAccess($coachUser, $residentUser->building);

            // and only give access for 3 users.
            if ($i < 3) {
                // give the coach access to the resident his building
                BuildingCoachStatusService::giveAccess($coachUser, $residentUser->building);
            }

            ++$i;
        });

        $connectedCoachesForBuilding = BuildingCoachStatusService::getConnectedCoachesByBuilding($residentUser->building);

        $this->assertCount(3, $connectedCoachesForBuilding);
    }

    private function user(Cooperation $cooperation): User
    {
        return User::factory()->create([
            'cooperation_id' => $cooperation->id,
            'account_id' => Account::factory()->create()->id,
        ]);
    }

    public function test_the_coach_shown_is_the_one_attached_most_recently(): void
    {
        $cooperation = Cooperation::factory()->create();
        $building = Building::factory()->create(['user_id' => $this->user($cooperation)->id]);

        $first = $this->user($cooperation);
        $second = $this->user($cooperation);

        BuildingCoachStatusService::giveAccess($first, $building);
        BuildingCoachStatusService::giveAccess($second, $building);

        $this->assertSame($second->id, BuildingCoachStatusService::getCoachToShow($building)?->id);
    }

    public function test_attaching_a_coach_again_makes_them_the_one_shown(): void
    {
        // Ordered on the coach's latest "added" row, not their first: a coach who was removed and
        // attached again is the most recent one.
        $cooperation = Cooperation::factory()->create();
        $building = Building::factory()->create(['user_id' => $this->user($cooperation)->id]);

        $first = $this->user($cooperation);
        $second = $this->user($cooperation);

        BuildingCoachStatusService::giveAccess($first, $building);
        BuildingCoachStatusService::giveAccess($second, $building);
        BuildingCoachStatusService::revokeAccess($first, $building);
        BuildingCoachStatusService::giveAccess($first, $building);

        $this->assertSame($first->id, BuildingCoachStatusService::getCoachToShow($building)?->id);
    }

    public function test_a_removed_coach_is_never_the_one_shown(): void
    {
        $cooperation = Cooperation::factory()->create();
        $building = Building::factory()->create(['user_id' => $this->user($cooperation)->id]);

        $staying = $this->user($cooperation);
        $leaving = $this->user($cooperation);

        BuildingCoachStatusService::giveAccess($staying, $building);
        BuildingCoachStatusService::giveAccess($leaving, $building);
        BuildingCoachStatusService::revokeAccess($leaving, $building);

        $this->assertSame($staying->id, BuildingCoachStatusService::getCoachToShow($building)?->id);
    }

    public function test_no_coach_means_nothing_to_show(): void
    {
        $cooperation = Cooperation::factory()->create();
        $building = Building::factory()->create(['user_id' => $this->user($cooperation)->id]);

        $this->assertNull(BuildingCoachStatusService::getCoachToShow($building));
    }
}
