<?php

namespace App\Services;

use App\Events\PrivateMessageReceiverEvent;
use App\Models\PrivateMessage;
use App\Models\PrivateMessageView;

class PrivateMessageViewService {

    /**
     * Create a private message view for each group participant
     *
     * @param PrivateMessage $privateMessage
     */
    public static function create(PrivateMessage $privateMessage)
    {
        event(new PrivateMessageReceiverEvent($privateMessage));
    }
}