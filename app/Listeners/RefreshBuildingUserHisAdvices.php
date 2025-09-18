<?php

namespace App\Listeners;

use App\Events\BuildingAddressUpdated;
use App\Helpers\Queue;
use App\Jobs\RefreshRegulationsForBuildingUser;

class RefreshBuildingUserHisAdvices extends NonHandleableListenerAfterReset
{
    public $queue = Queue::APP_HIGH;

    /**
     * Handle the event.
     */
    public function handle(BuildingAddressUpdated $event): void
    {
        $this->checkLastResetAt(function () use ($event) {
            RefreshRegulationsForBuildingUser::dispatch($event->building);
        }, $event->building);
    }
}
