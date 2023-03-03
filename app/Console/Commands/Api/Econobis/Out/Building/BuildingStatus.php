<?php

namespace App\Console\Commands\Api\Econobis\Out\Building;

use App\Models\Building;
use App\Services\Econobis\Client;
use App\Services\Econobis\Econobis;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class BuildingStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:econobis:out:building:status {building : The id of the building you would like to process.}';

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
        $building = Building::findOrFail($this->argument('building'));

        $mostRecentStatus = $building->getMostRecentBuildingStatus();

        $data = [
            'account_related' => [
                'building_id' => $building->id,
                'user_id' => $building->user->id,
                'account_id' => $building->user->account_id,
                'contact_id' => $building->user->extra['contact_id'] ?? null,
            ],
        ];

        $data = array_merge($data, ['status' => $mostRecentStatus->status->only('id', 'short', 'name')]);


        $logger = \Illuminate\Support\Facades\Log::getLogger();
        $client = Client::init($logger);
        $econobis = Econobis::init($client);

        $response = $econobis->hoomdossier()->woningStatus($data);

        Log::debug('Response', $response);

        return 0;
    }
}
