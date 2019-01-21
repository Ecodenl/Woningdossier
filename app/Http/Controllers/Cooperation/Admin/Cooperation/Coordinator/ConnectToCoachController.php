<?php

namespace App\Http\Controllers\Cooperation\Admin\Cooperation\Coordinator;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Cooperation\Coordinator\ConnectToCoachRequest;
use App\Models\Building;
use App\Models\BuildingCoachStatus;
use App\Models\BuildingPermission;
use App\Models\Cooperation;
use App\Models\PrivateMessage;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ConnectToCoachController extends Controller
{
    /**
     * Show the coordinator all open conversation requests.
     *
     * @param Cooperation $cooperation
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Cooperation $cooperation)
    {
        $privateMessageBuildingIds = PrivateMessage::forMyCooperation()
            ->groupBy('building_id')
            ->select('building_id')
            ->get()
            ->toArray();

        $flattenedBuildingIds = array_flatten($privateMessageBuildingIds);

        $buildings = Building::findMany($flattenedBuildingIds);

        return view('cooperation.admin.cooperation.coordinator.connect-to-coach.index', compact('buildings'));
    }

    /**
     * Show the coordinator the form to connect a coach to a resident that has an open request.
     *
     * @param Cooperation $cooperation
     * @param $privateMessageId
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(Cooperation $cooperation, $buildingId)
    {
        $privateMessage = PrivateMessage::forMyCooperation()->conversationRequest($buildingId)->first();
        $coaches = $cooperation->getCoaches()->get();

        return view('cooperation.admin.cooperation.coordinator.connect-to-coach.create', compact('buildingId', 'coaches', 'privateMessage'));
    }

    /**
     * Connect a coach to a building and resident.
     *
     * @param Cooperation           $cooperation
     * @param ConnectToCoachRequest $request
     *
     * @return RedirectResponse
     */
    public function store(Cooperation $cooperation, ConnectToCoachRequest $request)
    {
        $coachId = $request->get('coach_id', '');
        $buildingId = $request->get('building_id', '');


        // the receiver of the message
        $coach = $cooperation->users()->find($coachId);

        if ($coach instanceof User) {

            $residentBuilding = Building::find($buildingId);

            $privateMessage = PrivateMessage::forMyCooperation()->conversationRequest($buildingId)->first();

            if ($privateMessage->allow_access) {
                // give the coach permission to the resident his building
                BuildingPermission::create([
                    'user_id' => $coach->id, 'building_id' => $residentBuilding->id,
                ]);
            }

            BuildingCoachStatus::create([
                'coach_id' => $coach->id,
                'building_id' => $residentBuilding->id,
                'status' => BuildingCoachStatus::STATUS_ACTIVE,
            ]);
        }

        return redirect()->route('cooperation.admin.cooperation.coordinator.connect-to-coach.index')->with('success', __('woningdossier.cooperation.admin.cooperation.coordinator.connect-to-coach.store.success'));
    }

    /**
     * When the coordinator decides to message the coach before attaching anything to the user.
     *
     * @param Cooperation $cooperation
     * @param int         $privateMessageId
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function talkToCoachCreate(Cooperation $cooperation, $privateMessageId)
    {
        $coaches = $cooperation->getCoaches()->get();

        return view('cooperation.admin.cooperation.coordinator.connect-to-coach.talk-to-coach', compact('coaches', 'privateMessageId'));
    }

    /**
     * Send a message to a coach without attaching anything to the user.
     *
     * @param Cooperation $cooperation
     * @param Request     $request
     *
     * @return RedirectResponse
     */
    public function talkToCoachStore(Cooperation $cooperation, Request $request)
    {
//        $coach = $request->get('coach', '');
//        $message = $request->get('message');
//        $title = $request->get('title', '');
//        $privateMessageId = $request->get('private_message_id', '');
//
//        // When a coordinator starts a message with a coach through a specific conversation request
//        // we update the status of that request to "in consideration"
//        PrivateMessage::openCooperationConversationRequests()->where('id', $privateMessageId)->update([
//
//        ]);
//
//        PrivateMessage::conversationRequest($buildingId)->update([
//            'status' => PrivateMessage::STATUS_IN_CONSIDERATION,
//        ]);
//
//        // the receiver of the message, in this case a coach
//        $toUser = $cooperation->users()->find($coach);
//
//        // create a new message
//        PrivateMessage::create(
//            [
//                'title' => $title,
//                'message' => $message,
//                'from_cooperation_id' => $cooperation->id,
//                'to_user_id' => $toUser->id,
//                'from_user_id' => \Auth::id(),
//            ]
//        );

        return redirect()->route('cooperation.admin.cooperation.coordinator.connect-to-coach.index')->with('success', __('woningdossier.cooperation.admin.cooperation.coordinator.connect-to-coach.store.success'));
    }
}
