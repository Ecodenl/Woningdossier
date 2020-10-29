<?php

namespace App\Observers;

use App\Models\PrivateMessage;
use App\Services\PrivateMessageViewService;

class PrivateMessageObserver
{
    /**
     * On updating check if the allow access is dirty, if so we need to change permissions and building_coach_statuses.
     *
     * @throws \Exception
     */
    public function updating(PrivateMessage $privateMessage)
    {
        \Log::debug('PrivateMessage: updating event is fired.');
    }

    /**
     * For every message that is created we want to create a row in the private_message_view.
     */
    public function created(PrivateMessage $privateMessage)
    {
        PrivateMessageViewService::create($privateMessage);
    }
}
