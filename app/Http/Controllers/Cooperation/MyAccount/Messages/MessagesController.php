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

        return redirect(route('cooperation.my-account.messages.edit'));

        return view('cooperation.my-account.messages.index', compact('myUnreadMessages', 'groups'));
    }

    public function edit(Cooperation $cooperation)
    {
        $buildingId = HoomdossierSession::getBuilding();
        $privateMessages = PrivateMessage::forMyCooperation()
            ->public()
            ->conversation($buildingId)
            ->get();

        // if no private message exist redirect them to the conversation request create
        if (!$privateMessages->first() instanceof PrivateMessage) {
            return redirect()->route('cooperation.conversation-requests.index');
        }
        $this->authorize('edit', $privateMessages->first());

        $groupParticipants = PrivateMessage::getGroupParticipants($buildingId);

        return view('cooperation.my-account.messages.edit', compact('privateMessages', 'buildingId', 'groupParticipants'));
    }

    public function store(ChatRequest $request)
    {
        MessageService::create($request);

        return redirect()->back();
    }

    public function revokeAccess(Cooperation $cooperation, Request $request)
    {
        // get the group participant user id which is only a coach, but still
        $groupParticipantUserId = $request->get('user_id');
        // get the building owner id
        $buildingOwnerId = $request->get('building_owner_id');

        // the building from the user / resident
        $building = Building::find($buildingOwnerId);


        if ($building instanceof Building) {

            // revoke the access for the coach to talk with the resident
            BuildingPermissionService::revokePermission($groupParticipantUserId, $building->id);
            BuildingCoachStatusService::revokeAccess($groupParticipantUserId, $building->id);

            // TODO: create a message ? to notify some admin ?
        }

        return redirect()->back();
    }


}
