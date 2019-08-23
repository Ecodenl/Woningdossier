<?php

namespace App\Http\Controllers\Cooperation;

use App\Models\PrivateMessageView;
use App\Http\Controllers\Controller;

class MessagesController extends Controller
{
    /**
     * Simple method to retrieve the total unread messages of a user
     *
     * @return int
     */
    public function getTotalUnreadMessageCount()
    {
        return response()->json(['count' => PrivateMessageView::getTotalUnreadMessagesForCurrentRole()]);
    }
}
