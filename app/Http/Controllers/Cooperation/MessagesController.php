<?php

namespace App\Http\Controllers\Cooperation;

use App\Http\Controllers\Controller;
use App\Helpers\Models\PrivateMessageViewHelper;

class MessagesController extends Controller
{
    /**
     * Simple method to retrieve the total unread messages of a user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTotalUnreadMessageCount()
    {
        return response()->json(['count' => PrivateMessageViewHelper::getTotalUnreadMessagesForCurrentRole()]);
    }
}
