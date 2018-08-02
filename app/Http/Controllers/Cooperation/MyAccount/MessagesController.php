<?php

namespace App\Http\Controllers\Cooperation\MyAccount;

use App\Models\Cooperation;
use App\Models\PrivateMessage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MessagesController extends Controller
{
    public function index()
    {
        $messages = PrivateMessage::all();
        $incomingMessages = PrivateMessage::myPrivateMessages()->get();

        return view('cooperation.my-account.messages.index', compact('messages', 'incomingMessages'));
    }

    public function edit(Cooperation $cooperation)
    {

        $privateMessages = PrivateMessage::coachConversation()->get();

        return view('cooperation.my-account.messages.edit', compact('privateMessages'));
    }
}
