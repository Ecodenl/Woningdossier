<?php

namespace App\Listeners;

use App\Jobs\RefreshRegulationsForBuildingUser;

class RefreshBuildingUserHisAdvices extends CanceableListener
{
    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $this->checkLastResetAt(function () use ($event) {
            RefreshRegulationsForBuildingUser::dispatch($event->building);
        }, $event->building);
    }
}
