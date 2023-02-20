<?php

namespace App\Listeners;

use App\Models\Municipality;
use App\Services\MappingService;
use App\Services\UserActionPlanAdviceService;
use Illuminate\Contracts\Queue\ShouldQueue;

class RefreshUserHisAdvices implements ShouldQueue
{
    protected $mappingService;
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(MappingService $mappingService)
    {
        $this->mappingService = $mappingService;
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        // refreshing this has no use when there is no municipality and or mapping for it.
        if ($event->building->municipality instanceof Municipality) {
            $municipality = $event->building->municipality;
            if ($this->mappingService->from($municipality)->exists()) {
                UserActionPlanAdviceService::init()->forUser($event->building->user)->refreshUserRegulations();
            }
        }
    }
}
