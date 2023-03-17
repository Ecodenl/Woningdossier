<?php

namespace Tests\Feature\app\Http\Controllers\Cooperation\Admin\Cooperation\CooperationAdmin;

use App\Helpers\HoomdossierSession;
use App\Helpers\RoleHelper;
use App\Models\Account;
use App\Models\Building;
use App\Models\Cooperation;
use App\Models\CooperationMeasureApplication;
use App\Models\CustomMeasureApplication;
use App\Models\InputSource;
use App\Models\MeasureCategory;
use App\Models\Role;
use App\Models\User;
use App\Models\UserActionPlanAdvice;
use App\Services\MappingService;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
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
        // Due to model events, the cooperation might get cooperation measures. We don't want them for this test.
        // We do not use truncate, as that resets the auto increment and borks the transaction.
        DB::table('cooperation_measure_applications')->delete();

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

        $cooperationMeasureApplication = CooperationMeasureApplication::factory()
            ->create([
                'cooperation_id' => $cooperation->id,
                'is_extensive_measure' => false,
                'is_deletable' => true,
            ]);
        $secondCooperationMeasureApplication = CooperationMeasureApplication::factory()
            ->create([
                'cooperation_id' => $cooperation->id,
                'is_extensive_measure' => false,
                'is_deletable' => true,
            ]);

        $measureCategory = MeasureCategory::factory()->create();

        MappingService::init()->from($cooperationMeasureApplication)
            ->sync([$measureCategory]);

        $residentInputSource = InputSource::findByShort(InputSource::RESIDENT_SHORT);
        $coachInputSource = InputSource::findByShort(InputSource::COACH_SHORT);
        $masterInputSource = InputSource::findByShort(InputSource::MASTER_SHORT);

        foreach ([$residentInputSource, $coachInputSource] as $inputSource) {
            UserActionPlanAdvice::factory()
                ->create([
                    'user_id' => $resident->id,
                    'input_source_id' => $inputSource->id,
                    'user_action_plan_advisable_type' => CooperationMeasureApplication::class,
                    'user_action_plan_advisable_id' => $cooperationMeasureApplication->id,
                ]);
            UserActionPlanAdvice::factory()
                ->create([
                    'user_id' => $resident->id,
                    'input_source_id' => $inputSource->id,
                    'user_action_plan_advisable_type' => CooperationMeasureApplication::class,
                    'user_action_plan_advisable_id' => $secondCooperationMeasureApplication->id,
                ]);
        }

        $this->assertDatabaseCount('mappings', 1);
        $this->assertDatabaseHas('mappings', [
            'from_model_type' => CooperationMeasureApplication::class,
            'from_model_id' => $cooperationMeasureApplication->id,
            'target_model_type' => MeasureCategory::class,
            'target_model_id' => $measureCategory->id,
        ]);
        $this->assertDatabaseCount('cooperation_measure_applications', 2);
        $this->assertDatabaseCount('custom_measure_applications', 0);

        // NOTE: Because of the GetMyValuesTrait, we will end up with a total of 6 advices!
        $this->assertDatabaseCount('user_action_plan_advices', 6);

        // NOTE: We run tests in sync, so after this delete request, the HandleCooperationMeasureApplicationDeletion
        // command has already happened!
        $this->delete(
            route(
                'cooperation.admin.cooperation.cooperation-admin.cooperation-measure-applications.destroy',
                compact('cooperation', 'cooperationMeasureApplication')
            )
        );

        // Assert it was deleted, and that there's now 3 custom measures for in its place.
        $this->assertDatabaseCount('mappings', 1);
        $this->assertDatabaseMissing('mappings', [
            'from_model_type' => CooperationMeasureApplication::class,
            'from_model_id' => $cooperationMeasureApplication->id,
            'target_model_type' => MeasureCategory::class,
            'target_model_id' => $measureCategory->id,
        ]);
        $this->assertDatabaseCount('cooperation_measure_applications', 1);
        $this->assertDatabaseMissing('cooperation_measure_applications', ['id' => $cooperationMeasureApplication->id]);
        $this->assertDatabaseHas('cooperation_measure_applications', ['id' => $secondCooperationMeasureApplication->id]);
        $this->assertDatabaseCount('custom_measure_applications', 3);

        // Assert there's still 6 advices.
        $this->assertDatabaseCount('user_action_plan_advices', 6);

        // Assert the database changes.
        $this->assertDatabaseMissing('user_action_plan_advices', [
            'user_id' => $resident->id,
            'user_action_plan_advisable_type' => CooperationMeasureApplication::class,
            'user_action_plan_advisable_id' => $cooperationMeasureApplication->id,
        ]);

        foreach ([$residentInputSource, $coachInputSource, $masterInputSource] as $inputSource) {
            $this->assertDatabaseHas('user_action_plan_advices', [
                'user_id' => $resident->id,
                'input_source_id' => $inputSource->id,
                'user_action_plan_advisable_type' => CooperationMeasureApplication::class,
                'user_action_plan_advisable_id' => $secondCooperationMeasureApplication->id,
            ]);
        }

        $customMeasures = $residentBuilding->customMeasureApplications()->withoutGlobalScopes()->get();
        foreach ($customMeasures as $customMeasure) {
            $this->assertSame($customMeasure->name, $cooperationMeasureApplication->name);
            $this->assertDatabaseHas('user_action_plan_advices', [
                'user_id' => $resident->id,
                'input_source_id' => $customMeasure->input_source_id,
                'user_action_plan_advisable_type' => CustomMeasureApplication::class,
                'user_action_plan_advisable_id' => $customMeasure->id,
            ]);
        }

        // Finally, assert mapping was also copied over
        $this->assertDatabaseHas('mappings', [
            'from_model_type' => CustomMeasureApplication::class,
            'from_model_id' => $residentBuilding->customMeasureApplications()->withoutGlobalScopes()->forInputSource($masterInputSource)->first()->id,
            'target_model_type' => MeasureCategory::class,
            'target_model_id' => $measureCategory->id,
        ]);
    }
}
