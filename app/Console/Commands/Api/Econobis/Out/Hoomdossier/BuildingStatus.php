<?php

namespace App\Console\Commands\Api\Econobis\Out\Hoomdossier;

use App\Jobs\Econobis\Out\SendBuildingStatusToEconobis;
use App\Models\Building;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class BuildingStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:econobis:out:hoomdossier:status {building : The id of the building you would like to process.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send the current status of the building.';

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
    public function handle()
    {
        Log::debug(__CLASS__);
        SendBuildingStatusToEconobis::dispatch(
            Building::findOrFail($this->argument('building'))
        );
        return 0;
    }
}
