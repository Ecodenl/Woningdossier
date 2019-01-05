<?php

namespace App\Services;

use App\Models\PrivateMessage;
use Illuminate\Http\Request;

class MessageService
{
    /**
     * Create a new message between a user and user.
     *
     * @param Request $request
     *
     * @return bool
     */
    public static function create(Request $request)
    {
        $message = $request->get('message', '');
        $receiverId = $request->get('receiver_id', '');
        $mainMessageId = $request->get('main_message_id', '');

        PrivateMessage::create(
            [
                'message' => $message,
                'from_user_id' => \Auth::id(),
                'to_user_id' => $receiverId,
                'main_message' => $mainMessageId,
            ]
        );

        return true;
    }
}
