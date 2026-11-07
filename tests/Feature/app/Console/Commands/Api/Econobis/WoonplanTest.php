<?php

namespace Tests\Feature\app\Console\Commands\Api\Econobis;

use App\Jobs\Econobis\Out\SendBuildingFilledInAnswersToEconobis;
use App\Jobs\Econobis\Out\SendUserActionPlanAdvicesToEconobis;
use App\Models\InputSource;
use App\Models\MeasureApplication;
use App\Models\Step;
use App\Models\User;
use App\Models\UserActionPlanAdvice;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

/**
 * The command joins user_action_plan_advices onto users, and both tables have an `extra` column.
 */
final class WoonplanTest extends TestCase
{
    use RefreshDatabase;

    public $seed = true;
    public $seeder = DatabaseSeeder::class;

    public function test_it_sends_the_woonplan_of_a_contact_whose_advices_changed(): void
    {
        Queue::fake();

        $user = User::first();
        $user->update(['allow_access' => true, 'extra' => ['contact_id' => 123]]);

        UserActionPlanAdvice::withoutEvents(fn () => UserActionPlanAdvice::create([
            'user_id' => $user->id,
            'input_source_id' => InputSource::findByShort(InputSource::MASTER_SHORT)->id,
            'user_action_plan_advisable_type' => MeasureApplication::class,
            'user_action_plan_advisable_id' => MeasureApplication::findByShort('cavity-wall-insulation')->id,
            'step_id' => Step::findByShort('wall-insulation')->id,
            'costs' => ['from' => null, 'to' => 1622.65],
            'extra' => ['smart_twin' => []],
        ]));

        $this->artisan('api:econobis:out:hoomdossier:woonplan')->assertSuccessful();

        Queue::assertPushed(SendUserActionPlanAdvicesToEconobis::class, fn ($job) => $job->building->is($user->building));
        Queue::assertPushed(SendBuildingFilledInAnswersToEconobis::class);
    }
}
