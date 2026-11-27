<?php

namespace Tests\Feature\app\Http\Controllers\Cooperation\MyAccount;

use App\Helpers\HoomdossierSession;
use App\Helpers\RoleHelper;
use App\Jobs\CheckBuildingAddress;
use App\Jobs\SmartTwin\Out\GetBuildingData;
use App\Models\Building;
use App\Models\Cooperation;
use App\Models\InputSource;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

/**
 * An address change in a cooperation with SmartTwin fetches SmartTwin's Building Data for the new
 * address (decision 2026-10-15-smarttwin-woninggegevens-building-data, B13). Saving the settings
 * without changing the address does not.
 */
final class SettingsControllerTest extends TestCase
{
    use RefreshDatabase;

    public bool $seed = true;
    public string $seeder = DatabaseSeeder::class;

    private Cooperation $cooperation;
    private Building $building;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('hoomdossier.services.smarttwin.enabled', true);
        Bus::fake([GetBuildingData::class, CheckBuildingAddress::class]);

        $this->cooperation = Cooperation::factory()->create(['smarttwin_enabled' => true])->fresh();
        $resident = User::factory()->withAccount()->asResident()->create(['cooperation_id' => $this->cooperation->id]);
        $this->building = Building::factory()->create([
            'user_id' => $resident->id, 'postal_code' => '5224HD', 'number' => '1', 'extension' => '',
            'street' => 'Teststraat', 'city' => "'s-Hertogenbosch",
        ]);

        $inputSource = InputSource::resident();
        $this->actingAs($resident->account);
        HoomdossierSession::setCooperation($this->cooperation);
        HoomdossierSession::setHoomdossierSessions($this->building, $inputSource, $inputSource, Role::findByName(RoleHelper::ROLE_RESIDENT));
    }

    private function save(string $number): TestResponse
    {
        return $this->put(route('cooperation.my-account.settings.update', ['cooperation' => $this->cooperation]), [
            'user'    => ['first_name' => 'Test', 'last_name' => 'Bewoner'],
            'address' => ['postal_code' => '5224HD', 'number' => $number, 'extension' => '', 'street' => 'Teststraat', 'city' => "'s-Hertogenbosch"],
        ]);
    }

    public function test_a_new_address_fetches_the_building_data(): void
    {
        $this->save('3')->assertSessionHasNoErrors();

        Bus::assertDispatched(GetBuildingData::class, fn (GetBuildingData $job) => $job->building->is($this->building)
            && $job->inputSource->short === InputSource::RESIDENT_SHORT
        );
    }

    public function test_the_same_address_does_not(): void
    {
        $this->save('1')->assertSessionHasNoErrors();

        Bus::assertNotDispatched(GetBuildingData::class);
    }
}
