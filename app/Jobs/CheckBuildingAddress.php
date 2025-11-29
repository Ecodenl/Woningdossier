<?php

namespace App\Jobs;

use App\Exceptions\BuildingAddressCheckFailedException;
use App\Jobs\Middleware\CheckLastResetAt;
use App\Helpers\Queue;
use App\Models\Building;
use App\Models\InputSource;
use App\Services\BuildingAddressService;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\ServerException;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Support\Facades\Log;

class CheckBuildingAddress extends NonHandleableJobAfterReset
{
    public Building $building;
    public InputSource $inputSource;

    public $tries = 5;

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
     */
    public function handle(BuildingAddressService $buildingAddressService): void
    {
        $building = $this->building;
        $addressContext = [
            'building_id' => $building->id,
            'postal_code' => $building->postal_code,
            'number' => $building->number,
            'extension' => $building->extension,
        ];

        try {
            $buildingAddressService->forBuilding($building)
                ->forInputSource($this->inputSource)
                ->updateAddress($building->only('postal_code', 'number', 'extension', 'street', 'city'))
                ->attachMunicipality()
                ->updateBuildingFeatures($building->only('postal_code', 'number', 'extension'));
        } catch (ServerException | ConnectException $e) {
            // Server errors (5xx) and connection issues are temporary - retry
            Log::warning("CheckBuildingAddress: Temporary BAG API error, will retry", $addressContext);
            $this->release(60);
            return;
        } catch (ClientException $e) {
            // Client errors (4xx) are permanent - no point in retrying
            // Note: The BAG API response is already logged in BagService::logBagException()
            Log::error("CheckBuildingAddress: Client error from BAG API, failing job", $addressContext);
            $this->fail(new BuildingAddressCheckFailedException($e->getMessage(), $e->getCode(), $e));
            return;
        }

        // Refresh the building to get updated data
        $building->refresh();

        // Check if we successfully attached a municipality
        if ($building->municipality()->exists()) {
            Log::debug("CheckBuildingAddress: Successfully attached municipality", $addressContext + [
                'municipality_id' => $building->municipality_id,
            ]);
            return;
        }

        // No municipality attached - determine why and whether to retry
        if (empty($building->bag_woonplaats_id)) {
            // No BAG data found - address likely doesn't exist in BAG, no point in retrying
            Log::warning("CheckBuildingAddress: No bag_woonplaats_id found, address may not exist in BAG", $addressContext);
            $this->fail(new BuildingAddressCheckFailedException("No BAG data found for address"));
            return;
        }

        // BAG data exists but no municipality mapping - this is a configuration issue, no point in retrying
        Log::warning("CheckBuildingAddress: Has bag_woonplaats_id but no municipality mapping", $addressContext + [
            'bag_woonplaats_id' => $building->bag_woonplaats_id,
        ]);
        $this->fail(new BuildingAddressCheckFailedException("No municipality mapping found for bag_woonplaats_id: {$building->bag_woonplaats_id}"));
    }

    public function middleware(): array
    {
        return [new CheckLastResetAt($this->building), (new WithoutOverlapping(sprintf('%s-%s', "CheckBuildingAddress", $this->building->id)))->releaseAfter(10)];
    }
}
