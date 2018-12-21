<?php

namespace App\Http\Controllers\Cooperation\Admin\Cooperation\Coordinator;

use App\Http\Requests\Admin\Cooperation\Coordinator\ConnectToCoachRequest;
use App\Models\Cooperation;
use App\Models\PrivateMessage;
use App\Models\User;
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

        return view('cooperation.admin.cooperation.coordinator.connect-to-coach.create', compact('privateMessage', 'coaches'));
    }


    /**
     * Send a message to the selected coach
     *
     * @param Cooperation $cooperation
     * @param ConnectToCoachRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Cooperation $cooperation, ConnectToCoachRequest $request)
    {
        $coach = $request->get('coach', '');
        $message = $request->get('message');
        $title = $request->get('title', '');

        $toUser = $cooperation->users()->find($coach);

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

        return redirect()->route('cooperation.admin.cooperation.coordinator.connect-to-coach.index')->with('success', __('woningdossier.cooperation.admin.cooperation.coordinator.connect-to-coach.store.success'));
    }
}
