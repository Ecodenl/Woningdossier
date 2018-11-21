<?php

namespace App\Http\Controllers\Cooperation\Admin\Cooperation\Coordinator;

use App\Models\Cooperation;
use App\Models\PrivateMessage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ConversationRequestsController extends Controller
{
    public function index()
    {
        $openConversationRequests = PrivateMessage::openCooperationConversationRequests()->get();

        return view('cooperation.admin.cooperation.coordinator.conversation-requests.index', compact('openConversationRequests'));
    }

    /**
     * Show the coordinator a message and set it as read
     *
     * @param Cooperation $cooperation
     * @param $messageId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Cooperation $cooperation, $messageId)
    {
        $privateMessage = PrivateMessage::openCooperationConversationRequests()->find($messageId);
        $privateMessage->to_user_read = true;
        $privateMessage->save();

        return view('cooperation.admin.cooperation.coordinator.conversation-requests.show', compact('privateMessage'));
    }

}
