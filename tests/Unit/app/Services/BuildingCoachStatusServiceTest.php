<?php

namespace Tests\Unit\app\Services;

use App\Models\Account;
use App\Models\Building;
use App\Models\Cooperation;
use App\Models\User;
use App\Services\BuildingCoachStatusService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BuildingCoachStatusServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\StatusesTableSeeder::class);
    }

    public function testGetConnectedBuildingsByUser()
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

    public function testGetConnectedCoachesByBuildingId()
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

        $connectedCoachesForBuilding = BuildingCoachStatusService::getConnectedCoachesByBuildingId($residentUser->building->id);

        $this->assertCount(3, $connectedCoachesForBuilding);
    }
}
