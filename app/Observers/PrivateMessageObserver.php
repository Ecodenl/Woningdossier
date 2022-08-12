<?php

namespace App\Observers;

use App\Models\PrivateMessage;
use App\Services\PrivateMessageViewService;

class PrivateMessageObserver
{
    /**
     * For every message that is created we want to create a row in the private_message_view.
     */
    public function created(PrivateMessage $privateMessage)
    {
        // PrivateMessageViewService::create($privateMessage);
    }
}
