<?php

namespace App\Jobs;

use App\Models\Building;
use App\Models\Municipality;
use App\Services\BuildingAddressService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;

class CheckBuildingAddress implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $building;

    public $tries = 10;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Building $building)
    {
        $this->building = $building;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(BuildingAddressService $buildingAddressService)
    {
        $building = $this->building;
        $buildingAddressService
            ->forBuilding($building)
            ->updateAddress($building->only('postal_code', 'number', 'extension', 'street', 'city'));
        $buildingAddressService->attachMunicipality();
        /**
         * requery it, no municipality can have multiple causes
         * - BAG is down
         * - Partial error, no bag_woonplaats_id (might be caused by faulty address from user or due to BAG outage)
         * - Partial error, no municipality string found in woonplaats endpoint
         */
        if (! $building->municipality()->first() instanceof Municipality) {
            $this->release(60);
        }
    }

    /**
     * Get the middleware the job should pass through.
     *
     * @return array
     */
    public function middleware()
    {
        return [new WithoutOverlapping(sprintf('%s-%s', "CheckBuildingAddress", $this->building->id))];
    }
}
