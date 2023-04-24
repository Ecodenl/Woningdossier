<?php

namespace App\Listeners;

use App\Events\BuildingAddressUpdated;
use App\Helpers\Queue;
use App\Jobs\RefreshRegulationsForBuildingUser;
use Illuminate\Contracts\Queue\ShouldQueue;

class RefreshBuildingUserHisAdvices implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        dd('Event handler '. __CLASS__);
        RefreshRegulationsForBuildingUser::dispatch($event->building);
    }
}
