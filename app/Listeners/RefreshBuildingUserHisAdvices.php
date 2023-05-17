<?php

namespace App\Listeners;

use App\Jobs\RefreshRegulationsForBuildingUser;
use Illuminate\Support\Facades\Log;

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
            Log::debug('Handling the Refresh method');
//            RefreshRegulationsForBuildingUser::dispatch($event->building);
        }, $event->building);
    }
}
