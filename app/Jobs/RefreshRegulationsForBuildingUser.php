<?php

namespace App\Jobs;

use App\Helpers\Queue;
use App\Models\Building;
use App\Models\Municipality;
use App\Services\MappingService;
use App\Services\UserActionPlanAdviceService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RefreshRegulationsForBuildingUser implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Building $building;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Building $building)
    {
        $this->building = $building;
        $this->queue = Queue::REGULATIONS;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(MappingService $mappingService)
    {
        // refreshing this has no use when there is no municipality and or mapping for it.
        if ($this->building->municipality instanceof Municipality) {
            $municipality = $this->building->municipality;
            if ($mappingService->from($municipality)->exists()) {
                UserActionPlanAdviceService::init()->forUser($this->building->user)->refreshUserRegulations();
            }
        }
    }
}
