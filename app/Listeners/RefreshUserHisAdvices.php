<?php

namespace App\Listeners;

use App\Services\UserActionPlanAdviceService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class RefreshUserHisAdvices implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        UserActionPlanAdviceService::init()->forUser($event->building->user)->refreshUserRegulations();
    }
}
