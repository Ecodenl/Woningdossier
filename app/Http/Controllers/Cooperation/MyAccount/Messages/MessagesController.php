<?php

namespace App\Http\Controllers\Cooperation\MyAccount\Messages;

use App\Helpers\HoomdossierSession;
use App\Http\Controllers\Controller;
use App\Http\Requests\ChatRequest;
use App\Models\Building;
use App\Models\BuildingCoachStatus;
use App\Models\Cooperation;
use App\Models\PrivateMessage;
use App\Models\User;
use App\Services\BuildingCoachStatusService;
use App\Services\BuildingPermissionService;
use App\Services\InboxService;
use App\Services\MessageService;
use Illuminate\Http\Request;

class MessagesController extends Controller
{
    public function index(Cooperation $cooperation)
    {
        // TODO: create a query instead of the merg
        // gives a idea of how it should be
        // but really, TODO!
        $mainMessages = PrivateMessage::where('is_completed', false)
            ->where('main_message', null)
            ->where('from_user_id', \Auth::id())
            ->orWhere('to_cooperation_id', HoomdossierSession::getCooperation())->get()->merge(PrivateMessage::mainMessages()->get());

        return view('cooperation.my-account.messages.index', compact('myUnreadMessages', 'mainMessages'));
    }

    public function edit(Cooperation $cooperation, $mainMessageId)
    {
        $this->authorize('edit', PrivateMessage::findOrFail($mainMessageId));

        $privateMessages = PrivateMessage::conversation($mainMessageId)->get();

        InboxService::setRead($mainMessageId);

        return view('cooperation.my-account.messages.edit', compact('privateMessages', 'mainMessageId'));
    }

    public function revokeAccess(Cooperation $cooperation, Request $request)
    {
        $currentChatMainMessage = $request->get('main_message_id');

        // the resident himself cannot start a chat with a coach, resident or whatsoever.
        // the main message is started from the coach or coordinator

        // this is NOT the request to the cooperation.
        $mainMessage = PrivateMessage::find($currentChatMainMessage);

        // the building from the user / resident
        $building = Building::where('user_id', $mainMessage->to_user_id)->first();

        // either the coach or the coordinator, or someone with a higher role than resident.
        $fromId = $mainMessage->from_user_id;

        // get the most recent conversation between that user and coach
        $buildingCoachStatus = BuildingCoachStatus::where('coach_id', $fromId)->where('building_id', $building->id)->get()->last();

        $privateMessageRequestId = $buildingCoachStatus->private_message_id;

        BuildingPermissionService::revokePermission($fromId, $building->id);

        // no coach connected so the status goes back to in consideration, the coordinator can take further actions from now on.
        PrivateMessage::find($privateMessageRequestId)->update(['status' => PrivateMessage::STATUS_IN_CONSIDERATION]);

        // revoke the access for the coach to talk with the resident
        BuildingCoachStatusService::revokeAccess($fromId, $building->id, $privateMessageRequestId);

        $sender = User::find($fromId);

        PrivateMessage::create([
            'from_user_id' => \Auth::id(),
            'to_cooperation_id' => HoomdossierSession::getCooperation(),
            'title' => \Auth::user()->first_name.' heeft de toegang van coach '.$sender->first_name.' ontzegt.',
            'message' => '',
        ]);

        return redirect()->back();
    }

    public function store(ChatRequest $request)
    {
        MessageService::create($request);

        return redirect()->back();
    }
}
