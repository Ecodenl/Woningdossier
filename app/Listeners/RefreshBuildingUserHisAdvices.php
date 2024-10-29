<?php

namespace App\Listeners;

use App\Jobs\RefreshRegulationsForBuildingUser;

class RefreshBuildingUserHisAdvices extends NonHandleableListenerAfterReset
{
    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        $this->checkLastResetAt(function () use ($event) {
            RefreshRegulationsForBuildingUser::dispatch($event->building);
        }, $event->building);
    }
}
