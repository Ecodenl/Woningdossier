<?php

namespace App\Http\Controllers\Cooperation;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Models\PrivateMessageView;

class MessagesController extends Controller
{
    /**
     * Simple method to retrieve the total unread messages of a user.
     */
    public function getTotalUnreadMessageCount(): JsonResponse
    {
        return response()->json(['count' => PrivateMessageView::getTotalUnreadMessagesForCurrentRole()]);
    }
}
