<?php

namespace App\Services;

use App\Events\PrivateMessageReceiverEvent;
use App\Helpers\HoomdossierSession;
use App\Models\PrivateMessage;
use App\Models\PrivateMessageView;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class PrivateMessageViewService
{
    /**
     * Create a private message view for each group participant.
     *
     * @param PrivateMessage $privateMessage
     */
    public static function create(PrivateMessage $privateMessage)
    {
        event(new PrivateMessageReceiverEvent($privateMessage));
    }

    /**
     * Sets the incoming messages to read.
     *
     * @param Collection $privateMessages
     *
     * @return bool
     */
    public static function setRead(Collection $privateMessages): bool
    {
        foreach ($privateMessages as $privateMessage) {
            $privateMessageQuery = PrivateMessageView::where('private_message_id', $privateMessage->id);

            if (\Auth::user()->hasRoleAndIsCurrentRole(['coordinator', 'cooperation-admin'])) {
                $privateMessageQuery
                    ->where('cooperation_id', HoomdossierSession::getCooperation())
                    ->update(['read_at' => Carbon::now()]);
            } else {
                $privateMessageQuery
                    ->where('user_id', \Auth::id())
                    ->update(['read_at' => Carbon::now()]);
            }
        }

        return true;
    }
}
