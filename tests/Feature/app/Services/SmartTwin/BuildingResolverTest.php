<?php

namespace Tests\Feature\app\Services\SmartTwin;

use App\Enums\SmartTwin\EventType;
use App\Models\Account;
use App\Models\Building;
use App\Models\BuildingCoachStatus;
use App\Models\Cooperation;
use App\Models\User;
use App\Services\SmartTwin\Api\UserRole;
use App\Services\SmartTwin\BuildingResolver;
use Database\Seeders\RoleTableSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class BuildingResolverTest extends TestCase
{
    use RefreshDatabase;

    private const SMARTTWIN_USER_ID = 'smarttwin-user-guid';

    private BuildingResolver $resolver;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleTableSeeder::class);

        $this->resolver = app(BuildingResolver::class);
    }

    public function test_it_resolves_the_residents_own_building(): void
    {
        $building = $this->residentBuilding($this->linkedAccount());

        $this->assertTrue(
            $building->is($this->resolver->resolve(EventType::RESIDENT_SCAN_FINISHED, $this->payload())),
        );
    }

    public function test_it_ignores_the_postal_code_notation(): void
    {
        $building = $this->residentBuilding($this->linkedAccount(), ['postal_code' => '5224 hd']);

        $this->assertTrue(
            $building->is($this->resolver->resolve(
                EventType::RESIDENT_SCAN_FINISHED,
                $this->payload(['PostalCode' => '5224HD']),
            )),
        );
    }

    public function test_it_glues_the_house_letter_and_addition_back_together(): void
    {
        $account = $this->linkedAccount();
        $this->residentBuilding($account, ['extension' => 'B']);

        // Two homes at one number: only the one whose extension matches may be picked.
        $wanted = $this->residentBuilding($account, ['extension' => 'a-3']);

        $resolved = $this->resolver->resolve(EventType::RESIDENT_SCAN_FINISHED, $this->payload([
            'HouseLetter'         => 'A',
            'HouseNumberAddition' => '3',
        ]));

        $this->assertTrue($wanted->is($resolved));
    }

    public function test_it_accepts_a_lone_candidate_whose_extension_does_not_match(): void
    {
        $building = $this->residentBuilding($this->linkedAccount(), ['extension' => 'A']);

        $resolved = $this->resolver->resolve(
            EventType::RESIDENT_SCAN_FINISHED,
            $this->payload(['HouseNumberAddition' => 'something else']),
        );

        $this->assertTrue($building->is($resolved));
    }

    public function test_it_refuses_when_more_than_one_candidate_matches(): void
    {
        $account = $this->linkedAccount();
        $this->residentBuilding($account, ['extension' => 'A']);
        $this->residentBuilding($account, ['extension' => 'A']);

        $this->assertNull($this->resolver->resolve(
            EventType::RESIDENT_SCAN_FINISHED,
            $this->payload(['HouseLetter' => 'A']),
        ));
    }

    public function test_it_refuses_a_building_the_account_is_not_attached_to(): void
    {
        $this->linkedAccount();

        // Someone else's building, at the address in the callback.
        $stranger = User::factory()->asResident()->create([
            'account_id'     => Account::factory()->create()->getKey(),
            'cooperation_id' => Cooperation::factory()->create()->getKey(),
        ]);
        Building::factory()->create([
            'user_id'     => $stranger->getKey(),
            'postal_code' => '5224HD',
            'number'      => '1',
            'extension'   => '',
        ]);

        $this->assertNull(
            $this->resolver->resolve(EventType::RESIDENT_SCAN_FINISHED, $this->payload()),
        );
    }

    public function test_it_does_not_mix_up_the_two_flows(): void
    {
        $this->residentBuilding($this->linkedAccount());

        // The resident's own home is not a coach flow result, even at a matching address.
        $this->assertNull(
            $this->resolver->resolve(EventType::COACH_SCAN_FINISHED, $this->payload()),
        );
    }

    public function test_it_resolves_a_coached_building(): void
    {
        $building = $this->coachedBuilding($this->linkedAccount());

        $this->assertTrue(
            $building->is($this->resolver->resolve(EventType::COACH_SCAN_FINISHED, $this->payload())),
        );
    }

    public function test_it_refuses_a_coach_who_was_removed_from_the_building(): void
    {
        $building = $this->coachedBuilding($account = $this->linkedAccount());

        $building->buildingCoachStatuses()->create([
            'coach_id' => $account->users()->firstOrFail()->getKey(),
            'status'   => BuildingCoachStatus::STATUS_REMOVED,
        ]);

        $this->assertNull(
            $this->resolver->resolve(EventType::COACH_SCAN_FINISHED, $this->payload()),
        );
    }

    public function test_it_refuses_a_coach_without_building_permission(): void
    {
        $building = $this->coachedBuilding($this->linkedAccount());
        $building->buildingPermissions()->delete();

        $this->assertNull(
            $this->resolver->resolve(EventType::COACH_SCAN_FINISHED, $this->payload()),
        );
    }

    public function test_it_refuses_when_the_resident_revoked_access(): void
    {
        $building = $this->coachedBuilding($this->linkedAccount());
        $building->user->update(['allow_access' => false]);

        $this->assertNull(
            $this->resolver->resolve(EventType::COACH_SCAN_FINISHED, $this->payload()),
        );
    }

    public function test_it_refuses_an_unknown_smarttwin_user(): void
    {
        $this->residentBuilding($this->linkedAccount());

        $this->assertNull($this->resolver->resolve(
            EventType::RESIDENT_SCAN_FINISHED,
            $this->payload(['UserId' => 'someone-else']),
        ));
    }

    public function test_it_refuses_a_callback_without_an_address(): void
    {
        $this->residentBuilding($this->linkedAccount());

        $this->assertNull($this->resolver->resolve(
            EventType::RESIDENT_SCAN_FINISHED,
            $this->payload(['PostalCode' => null, 'HouseNumber' => null]),
        ));
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function payload(array $data = []): array
    {
        return array_merge([
            'UserId'              => self::SMARTTWIN_USER_ID,
            'DossierId'           => 'dossier-guid',
            'PostalCode'          => '5224HD',
            'HouseNumber'         => '1',
            'HouseNumberAddition' => null,
            'HouseLetter'         => null,
        ], $data);
    }

    private function linkedAccount(): Account
    {
        return Account::factory()->create([
            'smarttwin_user_id'   => self::SMARTTWIN_USER_ID,
            'smarttwin_user_role' => UserRole::Resident,
        ]);
    }

    /**
     * @param  array<string, mixed>  $address
     */
    private function residentBuilding(Account $account, array $address = []): Building
    {
        $user = User::factory()->asResident()->create([
            'account_id'     => $account->getKey(),
            'cooperation_id' => Cooperation::factory()->create()->getKey(),
        ]);

        return $this->buildingAt($user, $address);
    }

    /**
     * A building owned by someone else, which this account coaches: active coach status, building
     * permission and a resident who allows access — the same three conditions the coach had to meet
     * to open the tool for it.
     */
    private function coachedBuilding(Account $account): Building
    {
        $cooperation = Cooperation::factory()->create();

        $coach = User::factory()->asCoach()->create([
            'account_id'     => $account->getKey(),
            'cooperation_id' => $cooperation->getKey(),
        ]);

        $resident = User::factory()->asResident()->create([
            'account_id'     => Account::factory()->create()->getKey(),
            'cooperation_id' => $cooperation->getKey(),
            'allow_access'   => true,
        ]);

        $building = $this->buildingAt($resident);

        $building->buildingCoachStatuses()->create([
            'coach_id' => $coach->getKey(),
            'status'   => BuildingCoachStatus::STATUS_ADDED,
        ]);
        $building->buildingPermissions()->create(['user_id' => $coach->getKey()]);

        return $building;
    }

    /**
     * @param  array<string, mixed>  $address
     */
    private function buildingAt(User $user, array $address = []): Building
    {
        return Building::factory()->create(array_merge([
            'user_id'     => $user->getKey(),
            'postal_code' => '5224HD',
            'number'      => '1',
            'extension'   => '',
        ], $address));
    }
}
