<?php

namespace App\Http\Controllers\Cooperation\MyAccount\Messages;

use App\Models\Cooperation;
use App\Models\PrivateMessage;
use App\Services\InboxService;
use App\Services\MessageService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MessagesController extends Controller
{
    public function index(Cooperation $cooperation)
    {

        $mainMessages = PrivateMessage::mainMessages()->get();

        $coachConversationRequest = PrivateMessage::myConversationRequest()->first();

        return view('cooperation.my-account.messages.index', compact('myUnreadMessages', 'mainMessages', 'coachConversationRequest'));
    }

    public function edit(Cooperation $cooperation, $mainMessageId)
    {

        $privateMessages = PrivateMessage::conversation($mainMessageId)->get();

        InboxService::setRead($mainMessageId);

        return view('cooperation.my-account.messages.edit', compact('privateMessages'));
    }

    public function store(Request $request)
    {
        MessageService::create($request);

        return redirect()->back();
    }

}
