<?php

namespace App\Http\Controllers\Cooperation;

use App\Helpers\Hoomdossier;
use App\Http\Controllers\Controller;
use App\Models\PrivateMessage;
use App\Models\PrivateMessageView;

class MessagesController extends Controller
{
    /**
     * Simple method to retrieve the total unread messages of a user.
     *
     * @return int
     */
    public function getTotalUnreadMessageCount()
    {
        $showCount = Hoomdossier::user()->can('view-any', PrivateMessage::class);

        return response()->json(['count' => PrivateMessageView::getTotalUnreadMessagesForCurrentRole(), 'showCount' => $showCount]);
    }
}
