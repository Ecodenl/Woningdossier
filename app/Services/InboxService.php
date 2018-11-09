<?php

namespace App\Services;


use App\Models\PrivateMessage;

class InboxService
{
    /**
     * Sets the incoming messages to read
     *
     * @param $mainMessageId
     * @return bool
     */
    public static function setRead($mainMessageId)
    {

        $mainMessage = PrivateMessage::find($mainMessageId);

        // if the user created the message we need to update the from_user_read
        // otherwise it would only work for the receiver of the message
        if ($mainMessage->isMyMessage()) {
            $userIdReadColumn = "from_user_id";
            $userReadColumn = "from_user_read";
        } else {
            $userIdReadColumn = "to_user_id";
            $userReadColumn = "to_user_read";
        }


        PrivateMessage::conversation($mainMessageId)->where($userIdReadColumn, \Auth::id())->update([
            $userReadColumn => true
        ]);

        return true;
    }



}