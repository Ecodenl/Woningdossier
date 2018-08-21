<?php

namespace App\Http\Controllers\Cooperation\Admin\Coach;

use App\Http\Requests\Cooperation\Admin\Coach\MessagesRequest;
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
        $myCreatedMessages = PrivateMessage::myCreatedMessages()->get();
        $mainMessages = PrivateMessage::mainMessages()->get();

        $mainMessages = collect($mainMessages)->merge($myCreatedMessages);

        return view('cooperation.admin.coach.messages.index', compact('mainMessages'));
    }

    public function edit(Cooperation $cooperation, $mainMessageId)
    {
        $privateMessages = PrivateMessage::conversation($mainMessageId)->get();

        InboxService::setRead($mainMessageId);

        return view('cooperation.admin.coach.messages.edit', compact('privateMessages'));
    }

    public function store(Cooperation $cooperation, MessagesRequest $request)
    {
        MessageService::create($request);

        return redirect()->back();
    }
}
