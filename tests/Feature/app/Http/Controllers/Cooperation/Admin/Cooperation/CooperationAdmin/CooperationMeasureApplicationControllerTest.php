<?php

namespace Tests\Feature\app\Http\Controllers\Cooperation\Admin\Cooperation\CooperationAdmin;

use App\Helpers\HoomdossierSession;
use App\Helpers\RoleHelper;
use App\Models\Account;
use App\Models\Building;
use App\Models\Cooperation;
use App\Models\CooperationMeasureApplication;
use App\Models\InputSource;
use App\Models\MeasureCategory;
use App\Models\Role;
use App\Models\User;
use App\Services\MappingService;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CooperationMeasureApplicationControllerTest extends TestCase
{
    use WithFaker,
        RefreshDatabase;

    public $seed = true;
    public $seeder = DatabaseSeeder::class;

    protected $followRedirects = true;

    public function test_destroying_a_measure_properly_converts_related_advices()
    {
        $cooperationAdminAccount = Account::factory()->create(['password' => Hash::make('secret')]);
        $residentAccount = Account::factory()->create(['password' => Hash::make('secret')]);
        $cooperation = Cooperation::factory()->create();

        $cooperationAdmin = User::factory()
            ->asCooperationAdmin()
            ->create([
                'cooperation_id' => $cooperation->id,
                'account_id' => $cooperationAdminAccount->id
            ]);

        $resident = User::factory()
            ->asCooperationAdmin()
            ->create([
                'cooperation_id' => $cooperation->id,
                'account_id' => $residentAccount->id
            ]);

        $cooperationAdminBuilding = Building::factory()
            ->create(['user_id' => $cooperationAdmin->id]);
        $residentBuilding = Building::factory()
            ->create(['user_id' => $resident->id]);

        $inputSource = InputSource::findByShort(InputSource::COOPERATION_SHORT);
        $role = Role::findByName(RoleHelper::ROLE_COOPERATION_ADMIN);

        $this->actingAs($cooperationAdminAccount);
        HoomdossierSession::setHoomdossierSessions($cooperationAdminBuilding, $inputSource, $inputSource, $role);

        $cooperationMeasure = CooperationMeasureApplication::factory()
            ->create([
                'is_extensive_measure' => false,
                'is_deletable' => true,
            ]);

        $measureCategory = MeasureCategory::factory()->create();

        MappingService::init()->from($cooperationMeasure)
            ->sync([$measureCategory]);

        //TODO:
        // - Create another resident
        // - Create 3 advices using the cooperation measure with 3 different input sources for both residents
        // - Delete the measure
        // - Assert 3 new custom measures (per resident)
        // - Assert 1 advice per measure (per resident)
        // - Assert master custom measure has mapping (per resident)
        // - Assert cooperation measure deleted
        // - Assert no advices with cooperation measure exist
    }
}
