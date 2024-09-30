<?php

namespace App\Listeners;

use App\Events\StepCleared;
use App\Services\UserActionPlanAdviceService;

class DeleteUserActionPlanAdvicesForStep
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
     */
    public function handle(StepCleared $event): void
    {
        UserActionPlanAdviceService::clearForStep($event->user, $event->inputSource, $event->step);
    }
}
