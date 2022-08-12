<?php

namespace App\Observers;

use App\Events\PrivateMessageReceiverEvent;
use App\Helpers\HoomdossierSession;
use App\Models\PrivateMessage;
use App\Services\PrivateMessageViewService;

class PrivateMessageObserver
{
    /**
     * For every message that is created we want to create a row in the private_message_view.
     */
    public function created(PrivateMessage $privateMessage)
    {
        PrivateMessageReceiverEvent::dispatch($privateMessage, HoomdossierSession::getCooperation(true), \Auth::user());
    }
}
