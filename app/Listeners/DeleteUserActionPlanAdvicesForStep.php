<?php

namespace App\Listeners;

use App\Events\StepCleared;
use App\Services\UserActionPlanAdviceService;
use Illuminate\Contracts\Queue\ShouldQueue;

class DeleteUserActionPlanAdvicesForStep implements ShouldQueue
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
    public function handle(StepCleared $event)
    {
        UserActionPlanAdviceService::clearForStep($event->user, $event->inputSource, $event->step);
    }
}
