<?php

namespace App\Listeners;

use App\Events\UserAssociatedWithOtherCooperation;
use App\Mail\UserAssociatedWithCooperation;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendUserAssociatedWithCooperationMail implements ShouldQueue
{
    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(UserAssociatedWithOtherCooperation $event)
    {
        \Mail::to($event->user->account->email)->sendNow(
            new UserAssociatedWithCooperation($event->cooperation, $event->user)
        );
    }
}
