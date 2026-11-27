<?php

namespace Tests\Feature\app\Http\Controllers\Cooperation\Admin\SuperAdmin;

use App\Enums\SmartTwin\EventType;
use App\Helpers\HoomdossierSession;
use App\Helpers\RoleHelper;
use App\Jobs\SmartTwin\ProcessAdviceResults;
use App\Models\Account;
use App\Models\Building;
use App\Models\Cooperation;
use App\Models\FileStorage;
use App\Models\InputSource;
use App\Models\Role;
use App\Models\User;
use App\Services\SmartTwin\AdviceResultStorage;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

final class SmartTwinControllerTest extends TestCase
{
    use RefreshDatabase;

    public $seed = true;
    public $seeder = DatabaseSeeder::class;

    private Cooperation $ownCooperation;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        Storage::fake('downloads');

        $this->ownCooperation = $this->actAsSuperAdmin();
    }

    public function test_index_lists_the_artifacts_of_every_cooperation(): void
    {
        $first = $this->buildingWithAdvice();
        $second = $this->buildingWithAdvice();

        $response = $this->get(route('cooperation.admin.super-admin.smart-twin.index', [
            'cooperation' => $this->ownCooperation,
        ]));

        $response->assertOk();
        $response->assertSee($first->getAddress());
        $response->assertSee($second->getAddress());
    }

    public function test_download_returns_the_stored_response(): void
    {
        $building = $this->buildingWithAdvice(['current' => ['energyLabel' => 'C']]);

        $response = $this->get(route('cooperation.admin.super-admin.smart-twin.download', [
            'cooperation' => $this->ownCooperation,
            'fileStorageId' => $this->fileStorageFor($building)->id,
        ]));

        $response->assertOk();
        $this->assertSame('C', json_decode($response->streamedContent(), true)['current']['energyLabel']);
    }

    public function test_reprocess_replays_the_mapping_for_the_building_and_flow(): void
    {
        Bus::fake([ProcessAdviceResults::class]);

        $building = $this->buildingWithAdvice();

        $response = $this->post(route('cooperation.admin.super-admin.smart-twin.reprocess', [
            'cooperation' => $this->ownCooperation,
            'fileStorageId' => $this->fileStorageFor($building)->id,
        ]));

        $response->assertRedirect();
        Bus::assertDispatched(
            ProcessAdviceResults::class,
            fn (ProcessAdviceResults $job) => EventType::RESIDENT_SCAN_FINISHED->value . "_{$building->id}" === $job->uniqueId(),
        );
    }

    public function test_page_is_out_of_reach_for_a_cooperation_admin(): void
    {
        $cooperation = Cooperation::factory()->create();
        $account = Account::factory()->create();
        $user = User::factory()->asCooperationAdmin()->create([
            'account_id' => $account->id,
            'cooperation_id' => $cooperation->id,
        ]);
        $building = Building::factory()->create(['user_id' => $user->id]);

        $this->actingAs($account);
        HoomdossierSession::setHoomdossierSessions(
            $building,
            InputSource::findByShort(InputSource::COOPERATION_SHORT),
            InputSource::findByShort(InputSource::COOPERATION_SHORT),
            Role::findByName(RoleHelper::ROLE_COOPERATION_ADMIN),
        );
        HoomdossierSession::setCooperation($cooperation);

        $response = $this->get(route('cooperation.admin.super-admin.smart-twin.index', compact('cooperation')));

        $response->assertRedirect();
        $this->followRedirects($response)
            ->assertDontSee(__('cooperation/admin/super-admin/smart-twin.index.description'));
    }

    private function actAsSuperAdmin(): Cooperation
    {
        $cooperation = Cooperation::factory()->create();
        $account = Account::factory()->create();
        $user = User::factory()->create([
            'account_id' => $account->id,
            'cooperation_id' => $cooperation->id,
        ]);
        $user->assignRole(RoleHelper::ROLE_SUPER_ADMIN);
        $building = Building::factory()->create(['user_id' => $user->id]);

        $this->actingAs($account);
        HoomdossierSession::setHoomdossierSessions(
            $building,
            InputSource::master(),
            InputSource::master(),
            Role::findByName(RoleHelper::ROLE_SUPER_ADMIN),
        );
        HoomdossierSession::setCooperation($cooperation);

        return $cooperation;
    }

    /**
     * @param  array<string, mixed>  $results
     */
    private function buildingWithAdvice(array $results = ['success' => true]): Building
    {
        $account = Account::factory()->create();
        $user = User::factory()->asResident()->create([
            'account_id' => $account->id,
            'cooperation_id' => Cooperation::factory()->create()->id,
        ]);
        $building = Building::factory()->create(['user_id' => $user->id]);

        app(AdviceResultStorage::class)->storeRaw($building, EventType::RESIDENT_SCAN_FINISHED, $results);

        return $building;
    }

    private function fileStorageFor(Building $building): FileStorage
    {
        return FileStorage::withExpired()
            ->allInputSources()
            ->where('building_id', $building->getKey())
            ->firstOrFail();
    }
}
