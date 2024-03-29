<?php

namespace App\Listeners;

use App\Events\UserAssociatedWithOtherCooperation;
use App\Helpers\Queue;
use App\Mail\UserAssociatedWithCooperation;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendUserAssociatedWithCooperationMail implements ShouldQueue
{
    public $queue = Queue::APP_EXTERNAL;

    /**
     * Handle the event.
     *
     * @param object $event
     *
     * @return void
     */
    public function handle(UserAssociatedWithOtherCooperation $event)
    {
        \Mail::to($event->user->account->email)->send(
            new UserAssociatedWithCooperation($event->cooperation, $event->user)
        );
    }
}
