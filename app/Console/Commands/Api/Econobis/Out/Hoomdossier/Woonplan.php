<?php

namespace App\Console\Commands\Api\Econobis\Out\Hoomdossier;

use App\Jobs\Econobis\Out\SendBuildingFilledInAnswersToEconobis;
use App\Models\Building;
use App\Models\User;
use App\Services\Econobis\EconobisService;
use App\Services\Econobis\Api\Client;
use App\Services\Econobis\Api\Econobis;
use App\Services\Econobis\Payloads\WoonplanPayload;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Services\Econobis\Payloads\BuildingStatusPayload as BuildingStatusPayload;

class Woonplan extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:econobis:out:hoomdossier:woonplan {building : The id of the building you would like to process.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send the current woonplan of the building.';

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
    public function handle(EconobisService $econobisService, Econobis $econobis)
    {
        $relevantLastChangedDate = Carbon::now()->subHours(12)->toDateTimeString();
        // we dont have to use any policy, because we do this in the query itself.

        // now left join the user action pla nadvices
        // and get the advices that have been older than 30 minutes
        // than send it.
        User::econobisContacts()

            ->where('allow_access', 1);
        return 0;
    }
}
