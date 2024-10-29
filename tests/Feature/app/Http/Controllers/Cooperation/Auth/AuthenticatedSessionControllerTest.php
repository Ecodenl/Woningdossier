<?php

namespace Tests\Feature\app\Http\Controllers\Cooperation\Auth;

use App\Helpers\MappingHelper;
use App\Jobs\CheckBuildingAddress;
use App\Jobs\RefreshRegulationsForBuildingUser;
use App\Models\Account;
use App\Models\Building;
use App\Models\Cooperation;
use App\Models\Municipality;
use App\Models\User;
use App\Services\MappingService;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Hash;
use Tests\MocksLvbag;
use Tests\TestCase;

final class AuthenticatedSessionControllerTest extends TestCase
{
    use WithFaker,
        MocksLvbag,
        RefreshDatabase;

    public $seed = true;
    public $seeder = DatabaseSeeder::class;

    protected $followRedirects = true;

    public function test_login_succeeds_with_valid_credentials(): void
    {
        $account = Account::factory()->create(['password' => Hash::make('secret')]);
        $cooperation = Cooperation::factory()->create();

        $user = User::factory()
            ->create([
                'cooperation_id' => $cooperation->id,
                'account_id' => $account->id
            ]);

        $building = Building::factory()
            ->create(['user_id' => $user->id]);

        $response = $this->post(
            route('cooperation.auth.login.submit', ['cooperation' => $cooperation]),
            ['email' => $account->email, 'password' => 'secret']
        );
        $this->assertAuthenticatedAs($account);
    }

    public function test_after_login_it_tries_to_attach_municipality_when_no_attached(): void
    {
        Bus::fake(CheckBuildingAddress::class);
        $account = Account::factory()->create(['password' => Hash::make('secret')]);
        $cooperation = Cooperation::factory()->create();

        $user = User::factory()
            ->create([
                'cooperation_id' => $cooperation->id,
                'account_id' => $account->id
            ]);

        Building::factory()
            ->create(['user_id' => $user->id]);

        $this->post(
            route('cooperation.auth.login.submit', ['cooperation' => $cooperation]),
            ['email' => $account->email, 'password' => 'secret']
        );
        $this->assertAuthenticatedAs($account);
        Bus::assertDispatched(CheckBuildingAddress::class);
    }

    public function test_regulations_refresh_after_municipality_has_been_attached_after_login(): void
    {
        $fallbackData = [
            'street' => $this->faker->streetName(),
            'number' => $this->faker->numberBetween(3, 22),
            'city' => 'bubba',
            'extension' => 'd',
            'postal_code' => $this->faker->postcode(),
        ];

        Bus::fake([RefreshRegulationsForBuildingUser::class]);
        $account = Account::factory()->create(['password' => Hash::make('secret')]);
        $cooperation = Cooperation::factory()->create();

        $user = User::factory()
            ->asResident()
            ->create([
                'cooperation_id' => $cooperation->id,
                'account_id' => $account->id
            ]);

        $building = Building::factory()
            ->create([
                'user_id' => $user->id,
                // the id doesnt really matter in this case as the endpoint will always return a valid value due to mock.
                'bag_woonplaats_id' => '1234',
            ]);

        $municipality = Municipality::factory()->create();

        $fromMunicipalityName = $this->faker->randomElement(['Hatsikidee-Flakkee', 'Hellevoetsluis', 'Haarlem', 'Hollywood']);
        $this->mockLvbagClientAdresUitgebreid($fallbackData)->mockLvbagClientWoonplaats($fromMunicipalityName)->createLvbagMock();

        MappingService::init()
            ->from($fromMunicipalityName)
            ->sync([$municipality], MappingHelper::TYPE_BAG_MUNICIPALITY);

        $this->assertDatabaseMissing('buildings', [
            'id' => $building->id,
            'municipality_id' => $municipality->id,
        ]);

        $this->post(
            route('cooperation.auth.login.submit', ['cooperation' => $cooperation]),
            ['email' => $account->email, 'password' => 'secret']
        );
        $this->assertAuthenticatedAs($account);
        $this->assertDatabaseHas('buildings', [
            'id' => $building->id,
            'municipality_id' => $municipality->id,
        ]);
        Bus::assertDispatched(RefreshRegulationsForBuildingUser::class);
    }

    public function test_regulations_do_not_refresh_when_no_municipality_attached(): void
    {
        Bus::fake([RefreshRegulationsForBuildingUser::class]);
        $account = Account::factory()->create(['password' => Hash::make('secret')]);
        $cooperation = Cooperation::factory()->create();

        $user = User::factory()
            ->create([
                'cooperation_id' => $cooperation->id,
                'account_id' => $account->id
            ]);

        Building::factory()
            ->create(['user_id' => $user->id]);

        $this->post(
            route('cooperation.auth.login.submit', ['cooperation' => $cooperation]),
            ['email' => $account->email, 'password' => 'secret']
        );
        $this->assertAuthenticatedAs($account);

        Bus::assertNotDispatched(RefreshRegulationsForBuildingUser::class);
    }

    public function test_regulations_only_refresh_when_municipality_attached(): void
    {
        Bus::fake([RefreshRegulationsForBuildingUser::class]);
        $account = Account::factory()->create(['password' => Hash::make('secret')]);
        $cooperation = Cooperation::factory()->create();

        $municipality = Municipality::factory()->create();

        $user = User::factory()
            ->create([
                'cooperation_id' => $cooperation->id,
                'account_id' => $account->id
            ]);

        Building::factory()->create(['user_id' => $user->id, 'municipality_id' => $municipality->id]);

        $this->post(
            route('cooperation.auth.login.submit', ['cooperation' => $cooperation]),
            ['email' => $account->email, 'password' => 'secret']
        );
        $this->assertAuthenticatedAs($account);

        Bus::assertDispatched(RefreshRegulationsForBuildingUser::class);
    }

}
