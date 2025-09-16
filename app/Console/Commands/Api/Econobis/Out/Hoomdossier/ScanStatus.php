<?php

namespace App\Console\Commands\Api\Econobis\Out\Hoomdossier;

use App\Models\Building;
use App\Models\InputSource;
use App\Models\Scan;
use App\Services\Econobis\Api\Client;
use App\Services\Econobis\Api\EconobisApi;
use App\Services\Econobis\EconobisService;
use App\Services\Econobis\Payloads\ScanStatusPayload;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ScanStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:econobis:out:hoomdossier:scan-status {building : The id of the building you would like to process.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send the current status of the building.';

    /**
     * Execute the console command.
     */
    public function handle(EconobisService $econobisService, EconobisApi $econobis): int
    {
        $building = Building::findOrFail($this->argument('building'));

        $response = $econobis
            ->forCooperation($building->user->cooperation)
            ->hoomdossier()
            ->scanStatus($econobisService->forBuilding($building)->getPayload(ScanStatusPayload::class));

        Log::debug('Response', $response);

        return self::SUCCESS;
    }
}
