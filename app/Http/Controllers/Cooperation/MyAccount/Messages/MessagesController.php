<?php

namespace App\Http\Controllers\Cooperation\MyAccount\Messages;

use App\Models\Cooperation;
use App\Models\PrivateMessage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MessagesController extends Controller
{
    public function index()
    {

//        $incomingMessages = PrivateMessage::myPrivateMessages()->orderBy('to_user_read')->get();

        $mainMessages = PrivateMessage::mainMessages()->get();

        $coachConversationRequest = PrivateMessage::myConversationRequest()->first();

        return view('cooperation.my-account.messages.index', compact('myUnreadMessages', 'mainMessages', 'coachConversationRequest'));
    }

    public function edit(Cooperation $cooperation, $mainMessageId)
    {

        $privateMessages = PrivateMessage::getConversation($mainMessageId);

        // get all the user his private messages and set them as read
        $incomingMessages = PrivateMessage::myPrivateMessages()->get();

        foreach ($incomingMessages as $incomingMessage) {
            $incomingMessage = PrivateMessage::find($incomingMessage->id);
            $incomingMessage->to_user_read = true;
            $incomingMessage->save();
        }


        return view('cooperation.my-account.messages.edit', compact('privateMessages'));
    }

    public function store(Request $request)
    {

        $message = $request->get('message', '');
        $receiverId = $request->get('receiver_id', '');
        $mainMessageId = $request->get('main_message_id', '');

        PrivateMessage::create(
            [
                'message' => $message,
                'from_user_id' => \Auth::id(),
                'to_user_id' => $receiverId,
                'main_message' => $mainMessageId
            ]
        );

        return redirect()->back();
    }

}
