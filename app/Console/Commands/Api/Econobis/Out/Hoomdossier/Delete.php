<?php

namespace App\Console\Commands\Api\Econobis\Out\Hoomdossier;

use App\Jobs\Econobis\Out\SendBuildingStatusToEconobis;
use App\Jobs\Econobis\Out\SendUserDeletedToEconobis;
use App\Models\Building;
use App\Services\Econobis\EconobisService;
use App\Services\Econobis\Api\EconobisApi;
use App\Services\Econobis\Payloads\AppointmentDatePayload;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class Delete extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:econobis:out:hoomdossier:delete {building : The id of the building you would like to process.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send the building that should be deleted';

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
    public function handle(EconobisService $econobisService)
    {
        SendUserDeletedToEconobis::dispatch(
            $econobisService->forBuilding(
                Building::find($this->argument('building'))
            )->resolveAccountRelated()
        );

        return 0;
    }
}
