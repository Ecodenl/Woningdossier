<?php

namespace App\Console\Commands\Api\Econobis\Out\Hoomdossier;

use App\Jobs\Econobis\Out\SendBuildingFilledInAnswersToEconobis;
use App\Jobs\Econobis\Out\SendUserActionPlanAdvicesToEconobis;
use App\Models\Building;
use App\Models\Integration;
use App\Models\User;
use App\Services\IntegrationProcessService;
use Illuminate\Console\Command;
use Illuminate\Database\Query\JoinClause;

class Woonplan extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:econobis:out:hoomdossier:woonplan';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send the "woonplan" (user action plan advices) to Econobis, will take all users that have changed their tool in the last 12 hours.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(IntegrationProcessService $integrationProcessService)
    {
        $integrationProcessService = $integrationProcessService
            ->forIntegration(Integration::findByShort('econobis'))
            ->forProcess(SendUserActionPlanAdvicesToEconobis::class);

        // first get all advices that have been updated in the past 30 minutes
        // than check if the user his advices weren't synced in the past 30 minutes
        $relevantLastChangedDate = \Carbon\Carbon::now()->subMinutes(config('hoomdossier.services.econobis.send_woonplan_after_change'));

        User::econobisContacts()
            ->select(['users.*'])
            ->has('building')
            ->where('allow_access', 1)
            ->join('user_action_plan_advices', function (JoinClause $join) use ($relevantLastChangedDate) {
                $join
                    ->on('user_action_plan_advices.user_id', '=', 'users.id')
                    ->where(
                        'user_action_plan_advices.updated_at',
                        '>=',
                        $relevantLastChangedDate
                    );
            })
            ->groupBy(['users.id'])
            ->chunkById(50, function ($users) use ($integrationProcessService, $relevantLastChangedDate) {
                foreach ($users as $user) {
                    if ($user->building instanceof Building) {
                        $lastSyncedAt = $integrationProcessService->forBuilding($user->building)->lastSyncedAt();

                        $shouldSync = false;
                        if (is_null($lastSyncedAt)) {
                            $shouldSync = true;
                        } elseif ($relevantLastChangedDate->gt($lastSyncedAt)) {
                            $shouldSync = true;
                        }

                        if ($shouldSync) {
                            SendBuildingFilledInAnswersToEconobis::dispatch($user->building);
                            SendUserActionPlanAdvicesToEconobis::dispatch($user->building);
                        }
                    }
                }
            }, 'users.id', 'id');

        return 0;
    }
}
