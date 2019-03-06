<?php

namespace App\Http\Controllers\Cooperation\MyAccount\Messages;

use App\Helpers\HoomdossierSession;
use App\Http\Controllers\Controller;
use App\Http\Requests\ChatRequest;
use App\Models\Cooperation;
use App\Models\PrivateMessage;
use App\Services\MessageService;
use App\Services\PrivateMessageViewService;
use Illuminate\Http\Request;

class MessagesController extends Controller
{
    public function index(Cooperation $cooperation)
    {
        return redirect(route('cooperation.my-account.messages.edit'));

//        return view('cooperation.my-account.messages.index', compact('myUnreadMessagesCount', 'groups'));
    }

    public function edit(Cooperation $cooperation)
    {
        $buildingId = HoomdossierSession::getBuilding();
        $privateMessages = PrivateMessage::forMyCooperation()
            ->public()
            ->conversation($buildingId)
            ->get();

        // if no private message exist redirect them to the conversation request create
        if (! $privateMessages->first() instanceof PrivateMessage) {
            return redirect()->route('cooperation.conversation-requests.index');
        }
        $this->authorize('edit', $privateMessages->first());

        $groupParticipants = PrivateMessage::getGroupParticipants($buildingId);

        PrivateMessageViewService::setRead($privateMessages);

        return view('cooperation.my-account.messages.edit', compact('privateMessages', 'buildingId', 'groupParticipants'));
    }

    public function store(ChatRequest $request)
    {
        MessageService::create($request);

        return redirect()->back();
    }
}
