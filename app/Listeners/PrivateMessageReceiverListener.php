<?php

namespace App\Listeners;

use App\Models\PrivateMessage;
use App\Models\PrivateMessageView;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class PrivateMessageReceiverListener
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
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $groupParticipants = PrivateMessage::getGroupParticipants($event->privateMessage->building_id);

        foreach ($groupParticipants as $groupParticipant) {
            // we do not set a row for ourself
            if ($groupParticipant->id != \Auth::id()) {
                PrivateMessageView::create([
                    'user_id' => $groupParticipant->id,
                    'private_message_id' => $event->privateMessage->id,
                ]);
            }
        }
    }
}
