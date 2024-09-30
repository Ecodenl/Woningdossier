<?php

namespace App\Jobs;

use App\Jobs\Middleware\CheckLastResetAt;
use App\Helpers\Queue;
use App\Models\Building;
use App\Models\InputSource;
use App\Models\Municipality;
use App\Services\BuildingAddressService;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Queue\MaxAttemptsExceededException;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Support\Facades\Log;

class CheckBuildingAddress extends NonHandleableJobAfterReset
{
    public Building $building;
    public InputSource $inputSource;

    public $tries = 10;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Building $building, InputSource $inputSource)
    {
        parent::__construct();
        $this->building = $building;
        $this->inputSource = $inputSource;
        $this->queue = Queue::APP_EXTERNAL;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(BuildingAddressService $buildingAddressService): void
    {
        $building = $this->building;
        try {
            $buildingAddressService->forBuilding($building)
                ->forInputSource($this->inputSource)
                ->updateAddress($building->only('postal_code', 'number', 'extension', 'street', 'city'))
                ->attachMunicipality()
                ->updateBuildingFeatures($building->only('postal_code', 'number', 'extension'));
        } catch (ClientException $e) {
            Log::debug("Exception: {$e->getMessage()}");
            // When there's a client exception there's no point in retrying.
            $this->fail($e);
            return;
        } catch (MaxAttemptsExceededException $e) {
            Log::debug(__METHOD__ . " - Building {$building->id}: " . $e->getMessage());
        }

        /**
         * Re-query it, no municipality can have multiple causes
         * - BAG is down
         * - Partial error, no bag_woonplaats_id (might be caused by faulty address from user or due to BAG outage)
         * - Partial error, no municipality string found in woonplaats endpoint
         */
        if (! $building->municipality()->first() instanceof Municipality) {
            $this->release(60);
        }
    }

    public function middleware(): array
    {
        return [new CheckLastResetAt($this->building), (new WithoutOverlapping(sprintf('%s-%s', "CheckBuildingAddress", $this->building->id)))->releaseAfter(10)];
    }
}
