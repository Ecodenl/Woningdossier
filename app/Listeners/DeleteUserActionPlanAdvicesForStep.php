<?php

namespace App\Listeners;

use App\Events\StepCleared;
use App\Services\UserActionPlanAdviceService;

class DeleteUserActionPlanAdvicesForStep
{
    /**
     * Handle the event.
     */
    public function handle(StepCleared $event): void
    {
        UserActionPlanAdviceService::clearForStep($event->user, $event->inputSource, $event->step);
    }
}
