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
        PrivateMessage::conversation($mainMessageId)->where('to_user_id', \Auth::id())->update([
            'to_user_read' => true
        ]);

        return true;
    }



}