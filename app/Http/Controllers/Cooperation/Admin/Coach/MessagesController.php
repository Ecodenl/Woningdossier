<?php

namespace App\Http\Controllers\Cooperation\Admin\Coach;

use App\Helpers\HoomdossierSession;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cooperation\Admin\Coach\MessagesRequest;
use App\Models\Building;
use App\Models\BuildingCoachStatus;
use App\Models\Cooperation;
use App\Models\PrivateMessage;
use App\Models\PrivateMessageView;
use App\Models\User;
use App\Services\BuildingCoachStatusService;
use App\Services\BuildingPermissionService;
use App\Services\InboxService;
use App\Services\MessageService;
use App\Services\PrivateMessageViewService;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;

class MessagesController extends Controller
{

    public function index()
    {
//         the raw query
//            SELECT bcs2.building_id, bcs2.count_active AS count_active, bcs3.count_removed AS count_removed
//            FROM ( SELECT building_id, count(`status`) AS count_active FROM building_coach_statuses WHERE coach_id = 16 AND `status` = 'active' group by building_id ) AS bcs2
//            LEFT JOIN
//            ( SELECT building_id, count(`status`) AS count_removed FROM building_coach_statuses WHERE coach_id = 16 AND `status` = 'removed' group by building_id) AS bcs3
//            ON bcs2.building_id = bcs3.building_id
        //    HAVING (count_active > count_removed) OR count_removed IS NULL

        // if laravel version = 5.6 use fromSub function.

        // get the building_coach_statuses
        $activeCount = \DB::raw('( SELECT building_id, count(`status`) AS count_active FROM building_coach_statuses WHERE coach_id = '.\Auth::id().' AND `status` = \'active\' group by building_id ) AS bcs2');
        $removedCount = \DB::raw('( SELECT building_id, count(`status`) AS count_removed FROM building_coach_statuses WHERE coach_id = '.\Auth::id().' AND `status` = \'removed\' group by building_id) AS bcs3');

        // get the building_coach_statuses where the active status is higher then the removed status
        $activeBuildingCoachStatuses = \DB::query()
            ->select('bcs2.building_id', 'bcs2.count_active AS count_active', 'bcs3.count_removed AS count_removed')
            ->from($activeCount)
            ->leftJoin($removedCount, 'bcs2.building_id', '=', 'bcs3.building_id')
            ->groupBy('count_active', 'count_removed', 'building_id')
            ->orHavingRaw('count_active > count_removed OR count_removed IS NULL')
            ->get();

        return view('cooperation.admin.coach.messages.index', compact('activeBuildingCoachStatuses'));
    }

    public function publicGroup(Cooperation $cooperation, $buildingId)
    {
        $isPublic = true;
        $privateMessages = PrivateMessage::forMyCooperation()->public()->conversation($buildingId)->get();

        if ($privateMessages->first() instanceof PrivateMessage) {
            $this->authorize('edit', $privateMessages->first());
        }

        PrivateMessageViewService::setRead($privateMessages);
        $groupParticipants = PrivateMessage::getGroupParticipants($buildingId);

        return view('cooperation.admin.coach.messages.edit', compact('privateMessages', 'buildingId', 'groupParticipants', 'isPublic'));
    }

    public function privateGroup(Cooperation $cooperation, $buildingId)
    {
        $isPublic = false;
        $privateMessages = PrivateMessage::forMyCooperation()->conversation($buildingId)->where('is_public', $isPublic)->get();

        if ($privateMessages->first() instanceof PrivateMessage) {
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

        return view('cooperation.admin.coach.messages.edit', compact('privateMessages', 'buildingId', 'groupParticipants', 'isPublic'));
    }


    public function store(Cooperation $cooperation, MessagesRequest $request)
    {
        MessageService::create($request);

        return redirect()->back();
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

        // either the coach or the coordinator, or someone with a higher role then resident.
        $fromId = $mainMessage->from_user_id;

        // get the most recent conversation between that user and coach
        $buildingCoachStatus = BuildingCoachStatus::where('coach_id', $fromId)->where('building_id', $building->id)->get()->last();

        $privateMessageRequestId = $buildingCoachStatus->private_message_id;

        // remove the building permission
        BuildingPermissionService::revokePermission($fromId, $building->id);

        // no coach connected so the status gos back to in consideration, the coordinator can take further actions from now on.
        PrivateMessage::find($privateMessageRequestId)->update(['status' => PrivateMessage::STATUS_IN_CONSIDERATION]);

        // revoke the access for the coach to talk with the resident
        BuildingCoachStatusService::revokeAccess($fromId, $building->id, $privateMessageRequestId);

        return redirect()->back();
    }
}
