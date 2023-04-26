<?php

namespace App\Listeners;

use App\Jobs\RefreshRegulationsForBuildingUser;
use App\Traits\Queue\CheckLastResetAt;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class RefreshBuildingUserHisAdvices implements ShouldQueue
{
    use CheckLastResetAt;

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $this->checkLastResetAt(function () use ($event) {
            Log::debug('Closure has been executed.');
            RefreshRegulationsForBuildingUser::dispatch($event->building);
        }, $event->building);
    }
}
