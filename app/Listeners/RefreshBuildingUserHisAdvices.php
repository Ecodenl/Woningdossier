<?php

namespace App\Listeners;

use App\Helpers\Queue;
use App\Jobs\RefreshRegulationsForBuildingUser;

class RefreshBuildingUserHisAdvices
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
        RefreshRegulationsForBuildingUser::dispatch($event->building)->onQueue(Queue::REGULATIONS);
    }
}
