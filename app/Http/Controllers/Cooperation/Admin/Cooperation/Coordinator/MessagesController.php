<?php

namespace App\Http\Controllers\Cooperation\Admin\Cooperation\Coordinator;

use App\Http\Requests\Cooperation\Admin\Coach\MessagesRequest;
use App\Models\Cooperation;
use App\Models\PrivateMessage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MessagesController extends Controller
{
    public function index()
    {
        $mainMessages = PrivateMessage::myCreatedMessages()->get();

        return view('cooperation.admin.cooperation.coordinator.messages.index', compact('mainMessages'));
    }

    public function edit(Cooperation $cooperation, $mainMessageId)
    {

        $privateMessages = PrivateMessage::getConversation($mainMessageId);


        $incomingMessages = $privateMessages->where('to_user_id', \Auth::id())->all();

        foreach ($incomingMessages as $incomingMessage) {
            $incomingMessage = PrivateMessage::find($incomingMessage->id);
            $incomingMessage->to_user_read = true;
            $incomingMessage->save();
        }


        return view('cooperation.admin.cooperation.coordinator.messages.edit', compact('privateMessages'));
    }

    public function store(Cooperation $cooperation, MessagesRequest $request)
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
