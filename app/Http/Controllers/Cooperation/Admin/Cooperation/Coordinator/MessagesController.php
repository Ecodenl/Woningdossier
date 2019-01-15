<?php

namespace App\Http\Controllers\Cooperation\Admin\Cooperation\Coordinator;

use App\Helpers\HoomdossierSession;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Cooperation\Coordinator\MessageRequest;
use App\Http\Requests\Cooperation\Admin\Coach\MessagesRequest;
use App\Models\Cooperation;
use App\Models\PrivateMessage;
use App\Services\InboxService;
use App\Services\MessageService;
use App\Services\PrivateMessageViewService;

class MessagesController extends Controller
{

    public function index()
    {
        // for now.
        return redirect(back());
//        return view('cooperation.admin.cooperation.coordinator.messages.index', compact('mainMessages'));
    }

    public function publicGroup(Cooperation $cooperation, $buildingId)
    {
        $isPublic = true;
        $privateMessages = PrivateMessage::forMyCooperation()->conversation($buildingId)->where('is_public', $isPublic)->get();

        if ($privateMessages instanceof PrivateMessage) {
            $this->authorize('edit', $privateMessages->first());
        }

        PrivateMessageViewService::setRead($privateMessages);
        $groupParticipants = PrivateMessage::getGroupParticipants($buildingId);

        return view('cooperation.admin.cooperation.coordinator.messages.edit', compact('privateMessages', 'isPublic', 'buildingId', 'groupParticipants'));
    }

    public function privateGroup(Cooperation $cooperation, $buildingId)
    {
        $isPublic = false;
        $privateMessages = PrivateMessage::forMyCooperation()->conversation($buildingId)->where('is_public', $isPublic)->get();

        if ($privateMessages instanceof PrivateMessage) {
            $this->authorize('edit', $privateMessages->first());
        } else {
            // at this point we check if there is actually a private_message, public or not.
            if (!PrivateMessage::forMyCooperation()->conversation($buildingId)->first() instanceof PrivateMessage) {
                // there are no messages for this building for the current cooperation, so we return them back to the index from the buildings
                return redirect()->route('cooperation.admin.cooperation.coordinator.building-access.index');
            }
        }

        PrivateMessageViewService::setRead($privateMessages);
        $groupParticipants = PrivateMessage::getGroupParticipants($buildingId);

        return view('cooperation.admin.cooperation.coordinator.messages.edit', compact('privateMessages', 'isPublic', 'buildingId', 'groupParticipants'));
    }

    public function edit(Cooperation $cooperation, $buildingId)
    {

    }

    public function store(Cooperation $cooperation, MessageRequest $request)
    {
        MessageService::create($request);

        return redirect()->back();
    }
}
