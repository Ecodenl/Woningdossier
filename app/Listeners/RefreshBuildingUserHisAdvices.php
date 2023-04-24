<?php

namespace App\Listeners;

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
        RefreshRegulationsForBuildingUser::dispatch($event->building);
    }
}
