<?php

namespace App\Http\Controllers\Cooperation\Admin\Cooperation\Coordinator;

use App\Http\Requests\Admin\Cooperation\Coordinator\ConnectToCoachRequest;
use App\Models\Building;
use App\Models\BuildingCoachStatus;
use App\Models\BuildingPermission;
use App\Models\Cooperation;
use App\Models\PrivateMessage;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ConnectToCoachController extends Controller
{
    /**
     * Show the coordinator all open conversation requests
     *
     * @param Cooperation $cooperation
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Cooperation $cooperation)
    {
        $openConversations = PrivateMessage::openCooperationConversationRequests()->get();

        return view('cooperation.admin.cooperation.coordinator.connect-to-coach.index', compact( 'openConversations'));
    }

    /**
     * Show the coordinator the form to connect a coach to a resident that has an open request
     *
     * @param Cooperation $cooperation
     * @param $senderId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(Cooperation $cooperation, $senderId)
    {
        $privateMessage = PrivateMessage::openCooperationConversationRequests()->where('from_user_id', $senderId)->first();

        $coaches = $cooperation->getCoaches()->get();

        return view('cooperation.admin.cooperation.coordinator.connect-to-coach.create', compact('privateMessage', 'coaches', 'senderId'));
    }


    /**
     * Send a message to the selected coach
     *
     * @param Cooperation $cooperation
     * @param ConnectToCoachRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeWithMessageToCoach(Cooperation $cooperation, ConnectToCoachRequest $request)
    {
        $coach = $request->get('coach', '');
        $message = $request->get('message');
        $title = $request->get('title', '');
        $senderId = $request->get('sender_id', "");


        // the resident now has a coach to talk to, so the conversation request is done.
        PrivateMessage::openCooperationConversationRequests()->where('from_user_id', $senderId)->update([
            'status' => PrivateMessage::STATUS_LINKED_TO_COACH
        ]);

        // the receiver of the message
        $toUser = $cooperation->users()->find($coach);

        // TODO: create a function that does the same as this, but without the message
        // so a coordinator can attach a coach to a resident in one click
        PrivateMessage::create(
            [
                'title' => $title,
                'request_type' => PrivateMessage::REQUEST_TYPE_COACH_CONVERSATION,
                'message' => $message,
                'from_cooperation_id' => $cooperation->id,
                'to_user_id' => $toUser->id,
                'from_user_id' => \Auth::id(),
            ]
        );

        $residentBuilding = Building::where('user_id', $senderId)->first();

        // give the coach permission to the resident his building
        BuildingPermission::create([
            'user_id' => $toUser->id, 'building_id' => $residentBuilding->id
        ]);

        // do not attach a status yet, the coach can do this himself in his gui
        BuildingCoachStatus::create([
            'coach_id' => $toUser->id, 'building_id' => $residentBuilding->id, 'status' => BuildingCoachStatus::STATUS_IN_CONSIDERATION
        ]);

        return redirect()->route('cooperation.admin.cooperation.coordinator.connect-to-coach.index')->with('success', __('woningdossier.cooperation.admin.cooperation.coordinator.connect-to-coach.store.success'));
    }

    /**
     * Send a message to the selected coach
     *
     * @param Cooperation $cooperation
     * @param ConnectToCoachRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeWithoutMessageToCoach(Cooperation $cooperation, ConnectToCoachRequest $request)
    {
        $coach = $request->get('coach', '');
        $senderId = $request->get('sender_id', "");


        // the resident now has a coach to talk to, so the conversation request is done.
        PrivateMessage::openCooperationConversationRequests()->where('from_user_id', $senderId)->update([
            'status' => PrivateMessage::STATUS_LINKED_TO_COACH
        ]);

        // the receiver of the message
        $toUser = $cooperation->users()->find($coach);

        $residentBuilding = Building::where('user_id', $senderId)->first();

        // give the coach permission to the resident his building
        BuildingPermission::create([
            'user_id' => $toUser->id, 'building_id' => $residentBuilding->id
        ]);

        BuildingCoachStatus::create([
            'coach_id' => $toUser->id, 'building_id' => $residentBuilding->id, 'status' => BuildingCoachStatus::STATUS_IN_CONSIDERATION
        ]);

        return redirect()->route('cooperation.admin.cooperation.coordinator.connect-to-coach.index')->with('success', __('woningdossier.cooperation.admin.cooperation.coordinator.connect-to-coach.store.success'));
    }

    /**
     * When the coordinator decides to message the coach before attaching anything to the user
     *
     * @param Cooperation $cooperation
     * @param integer $senderId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function talkToCoachCreate(Cooperation $cooperation, $senderId)
    {
        $coaches = $cooperation->getCoaches()->get();

        return view('cooperation.admin.cooperation.coordinator.connect-to-coach.talk-to-coach', compact('coaches', 'senderId'));
    }

    /**
     * Send a message to a coach without attaching anything to the user
     *
     * @param Cooperation $cooperation
     * @param Request $request
     * @return RedirectResponse
     */
    public function talkToCoachStore(Cooperation $cooperation, Request $request)
    {
        $coach = $request->get('coach', '');
        $message = $request->get('message');
        $title = $request->get('title', '');
        $senderId = $request->get('sender_id', "");

        // When a coordinator starts a message with a coach through a specific conversation request
        // we update the status of that request to "in consideration"
        PrivateMessage::openCooperationConversationRequests()->where('from_user_id', $senderId)->update([
            'status' => PrivateMessage::STATUS_IN_CONSIDERATION
        ]);

        // the receiver of the message, in this case a coach
        $toUser = $cooperation->users()->find($coach);

        // create a new message
        PrivateMessage::create(
            [
                'title' => $title,
                'message' => $message,
                'from_cooperation_id' => $cooperation->id,
                'to_user_id' => $toUser->id,
                'from_user_id' => \Auth::id(),
            ]
        );

        return redirect()->route('cooperation.admin.cooperation.coordinator.connect-to-coach.index')->with('success', __('woningdossier.cooperation.admin.cooperation.coordinator.connect-to-coach.store.success'));
    }
}
