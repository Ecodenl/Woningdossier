<?php

namespace Tests\Feature\app\Policies;

use App\Enums\SmartTwin\EventType;
use App\Helpers\HoomdossierSession;
use App\Helpers\RoleHelper;
use App\Models\Account;
use App\Models\Building;
use App\Models\Cooperation;
use App\Models\FileStorage;
use App\Models\InputSource;
use App\Models\Role;
use App\Models\User;
use App\Services\SmartTwin\AdviceResultStorage;
use App\Services\SmartTwin\SmartTwinFileTypes;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * The SmartTwin artifacts are super admin debug material, and are reachable through the super admin
 * page only. These tests pin down that the cooperation's own file storage routes never hand them
 * out, no matter who asks.
 */
final class FileStoragePolicyTest extends TestCase
{
    use RefreshDatabase;

    public $seed = true;
    public $seeder = DatabaseSeeder::class;

    private Cooperation $cooperation;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        Storage::fake('downloads');

        $this->cooperation = Cooperation::factory()->create();
    }

    public function test_a_resident_cannot_download_the_advice_of_their_own_building(): void
    {
        $resident = $this->userFor(RoleHelper::ROLE_RESIDENT);
        $fileStorage = $this->adviceFor($resident->building);

        $this->actAs($resident, RoleHelper::ROLE_RESIDENT, InputSource::RESIDENT_SHORT);

        $response = $this->get(route('cooperation.file-storage.download', [
            'cooperation' => $this->cooperation,
            'fileStorage' => $fileStorage,
        ]));

        $response->assertForbidden();
    }

    public function test_a_cooperation_admin_cannot_download_the_advice_of_a_building_in_their_cooperation(): void
    {
        $resident = $this->userFor(RoleHelper::ROLE_RESIDENT);
        $fileStorage = $this->adviceFor($resident->building);

        $admin = $this->userFor(RoleHelper::ROLE_COOPERATION_ADMIN);
        $this->actAs($admin, RoleHelper::ROLE_COOPERATION_ADMIN, InputSource::COOPERATION_SHORT);

        $this->assertTrue(
            Gate::forUser($admin->account)->denies('download', [$fileStorage, $resident->building]),
        );
    }

    public function test_a_cooperation_admin_cannot_have_the_advice_generated(): void
    {
        $admin = $this->userFor(RoleHelper::ROLE_COOPERATION_ADMIN);
        $this->actAs($admin, RoleHelper::ROLE_COOPERATION_ADMIN, InputSource::COOPERATION_SHORT);

        $response = $this->post(route('cooperation.file-storage.store', [
            'cooperation' => $this->cooperation,
            'fileType' => SmartTwinFileTypes::ADVICE_RAW,
        ]));

        $response->assertForbidden();
        $this->assertDatabaseMissing('file_storages', ['input_source_id' => InputSource::findByShort(
            InputSource::COOPERATION_SHORT
        )->id]);
    }

    private function adviceFor(Building $building): FileStorage
    {
        return app(AdviceResultStorage::class)->storeRaw(
            $building,
            EventType::RESIDENT_SCAN_FINISHED,
            ['success' => true],
        );
    }

    private function actAs(User $user, string $roleName, string $inputSourceShort): void
    {
        $inputSource = InputSource::findByShort($inputSourceShort);

        $this->actingAs($user->account);
        HoomdossierSession::setHoomdossierSessions(
            $user->building,
            $inputSource,
            $inputSource,
            Role::findByName($roleName),
        );
        HoomdossierSession::setCooperation($this->cooperation);
    }

    private function userFor(string $roleName): User
    {
        $user = User::factory()->create([
            'account_id' => Account::factory()->create()->id,
            'cooperation_id' => $this->cooperation->id,
        ]);
        $user->assignRole($roleName);
        Building::factory()->create(['user_id' => $user->id]);

        return $user->refresh();
    }
}
