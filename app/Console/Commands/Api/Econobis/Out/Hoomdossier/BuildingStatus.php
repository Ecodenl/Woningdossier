<?php

namespace App\Console\Commands\Api\Econobis\Out\Hoomdossier;

use App\Models\Building;
use App\Services\Econobis\EconobisService;
use App\Services\Econobis\Api\Client;
use App\Services\Econobis\Api\Econobis;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Services\Econobis\Payloads\BuildingStatusPayload;

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
    public function handle(EconobisService $econobisService)
    {
        $building = Building::findOrFail($this->argument('building'));

        $logger = \Illuminate\Support\Facades\Log::getLogger();
        $client = Client::init($logger);
        $econobis = Econobis::init($client);

        $response = $econobis
            ->hoomdossier()
            ->woningStatus(
                $econobisService->getPayload($building,BuildingStatusPayload::class)
            );

        Log::debug('Response', $response);

        return 0;
    }
}
