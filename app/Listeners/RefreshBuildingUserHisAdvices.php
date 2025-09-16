<?php

namespace App\Listeners;

use App\Events\BuildingAddressUpdated;
use App\Jobs\RefreshRegulationsForBuildingUser;

class RefreshBuildingUserHisAdvices extends NonHandleableListenerAfterReset
{
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
