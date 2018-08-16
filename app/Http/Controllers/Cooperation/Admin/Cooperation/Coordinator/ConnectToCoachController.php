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
    public function index(Cooperation $cooperation)
    {
        $coaches = $cooperation->getCoaches();

        return view('cooperation.admin.cooperation.coordinator.connect-to-coach.index', compact( 'coaches'));
    }

    public function create(Cooperation $cooperation, $senderId)
    {
        $privateMessage = PrivateMessage::openCooperationConversationRequests()->where('from_user_id', $senderId)->first();

        $coaches = $cooperation->getCoaches();


        return view('cooperation.admin.cooperation.coordinator.connect-to-coach.create', compact('privateMessage', 'coaches'));
    }

    public function store(Cooperation $cooperation, ConnectToCoachRequest $request)
    {
        $coach = $request->get('coach', '');
        $message = $request->get('message');
        $title = $request->get('title', '');

        $toUser = $cooperation->users()->find($coach);

        PrivateMessage::create(
            [
                'title' => \Auth::user()->first_name ." ". \Auth::user()->last_name. " " .$title,
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
