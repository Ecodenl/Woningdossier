<?php

namespace App\Listeners;

use App\Helpers\HoomdossierSession;
use App\Models\Cooperation;
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
     * @param  object $event
     * @return void
     */
    public function handle($event)
    {
        $groupParticipants = PrivateMessage::getGroupParticipants($event->privateMessage->building_id);

        // now we creat for every group participant a privatemessageview
        foreach ($groupParticipants as $groupParticipant) {
            if ($groupParticipant->id != \Auth::id()) {
                PrivateMessageView::create([
                    'private_message_id' => $event->privateMessage->id,
                    'user_id' => $groupParticipant->id
                ]);
            }
        }

        // since a cooperation is not a 'participant' of a chat we need to create a row for the manually
        PrivateMessageView::create([
            'private_message_id' => $event->privateMessage->id,
            'cooperation_id' => HoomdossierSession::getCooperation()
        ]);

    }
}
