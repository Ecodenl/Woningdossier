<?php

namespace Tests\Feature\app\Http\Controllers\Cooperation\Admin\Cooperation;

use App\Enums\SmartTwin\EventType;
use App\Helpers\HoomdossierSession;
use App\Helpers\RoleHelper;
use App\Models\Account;
use App\Models\Building;
use App\Models\Cooperation;
use App\Models\FileType;
use App\Models\InputSource;
use App\Models\Role;
use App\Models\User;
use App\Services\SmartTwin\AdviceResultStorage;
use App\Services\SmartTwin\SmartTwinFileTypes;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

final class ReportControllerTest extends TestCase
{
    use RefreshDatabase;

    public $seed = true;
    public $seeder = DatabaseSeeder::class;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        Storage::fake('downloads');
    }

    /**
     * The SmartTwin artifacts share the report file type category, but hold the raw data of a
     * single building. They have no place on the cooperation wide report page.
     */
    public function test_the_smart_twin_artifacts_are_not_listed(): void
    {
        $cooperation = Cooperation::factory()->create();
        $resident = $this->userFor($cooperation, RoleHelper::ROLE_RESIDENT);

        app(AdviceResultStorage::class)->storeRaw(
            $resident->building,
            EventType::RESIDENT_SCAN_FINISHED,
            ['success' => true],
        );

        $this->actAsCooperationAdmin($cooperation);

        $response = $this->get(route('cooperation.admin.cooperation.reports.index', compact('cooperation')));

        $response->assertOk();
        $response->assertDontSee(FileType::findByShort(SmartTwinFileTypes::ADVICE_RAW)->name);
    }

    private function actAsCooperationAdmin(Cooperation $cooperation): User
    {
        $user = $this->userFor($cooperation, RoleHelper::ROLE_COOPERATION_ADMIN);
        $inputSource = InputSource::findByShort(InputSource::COOPERATION_SHORT);

        $this->actingAs($user->account);
        HoomdossierSession::setHoomdossierSessions(
            $user->building,
            $inputSource,
            $inputSource,
            Role::findByName(RoleHelper::ROLE_COOPERATION_ADMIN),
        );
        HoomdossierSession::setCooperation($cooperation);

        return $user;
    }

    private function userFor(Cooperation $cooperation, string $roleName): User
    {
        $user = User::factory()->create([
            'account_id' => Account::factory()->create()->id,
            'cooperation_id' => $cooperation->id,
        ]);
        $user->assignRole($roleName);
        Building::factory()->create(['user_id' => $user->id]);

        return $user->refresh();
    }
}
