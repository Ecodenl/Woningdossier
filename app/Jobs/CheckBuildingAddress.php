<?php

namespace App\Jobs;

use App\Jobs\Middleware\CheckLastResetAt;
use App\Helpers\Queue;
use App\Models\Building;
use App\Models\Municipality;
use App\Services\BuildingAddressService;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Support\Facades\Log;

class CheckBuildingAddress extends NonHandleableJobAfterReset implements ShouldQueue
{
    public $building;

    public $tries = 3;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Building $building)
    {
        parent::__construct();
        $this->building = $building;
        $this->queue = Queue::APP_EXTERNAL;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(BuildingAddressService $buildingAddressService)
    {
        $building = $this->building;
        try {
            $buildingAddressService->forBuilding($building);
            $buildingAddressService->updateAddress($building->only('postal_code', 'number', 'extension', 'street', 'city'));
            $buildingAddressService->attachMunicipality();
            $buildingAddressService->updateBuildingFeatures($building->only('postal_code', 'number', 'extension'));
        } catch (ClientException $e) {
            Log::debug("Exception: {$e->getMessage()}");
            // When there's a client exception there's no point in retrying.
            $this->fail($e);
            return;
        }

        /**
         * requery it, no municipality can have multiple causes
         * - BAG is down
         * - Partial error, no bag_woonplaats_id (might be caused by faulty address from user or due to BAG outage)
         * - Partial error, no municipality string found in woonplaats endpoint
         */
        if ( ! $building->municipality()->first() instanceof Municipality) {
            $this->release(60);
        }
    }

    public function middleware(): array
    {
        return [new CheckLastResetAt($this->building), (new WithoutOverlapping(sprintf('%s-%s', "CheckBuildingAddress", $this->building->id)))->releaseAfter(10)];
    }
}
