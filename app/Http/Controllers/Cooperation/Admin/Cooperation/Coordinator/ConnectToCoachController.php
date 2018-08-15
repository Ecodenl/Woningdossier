<?php

namespace App\Http\Controllers\Cooperation\Admin\Cooperation\Coordinator;

use App\Models\Cooperation;
use App\Models\PrivateMessage;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ConnectToCoachController extends Controller
{
    public function index(Cooperation $cooperation, $senderId)
    {
        $privateMessage = PrivateMessage::openCooperationConversationRequests()->where('from_user_id', $senderId)->first();

        $coaches = $cooperation->getCoaches();

        return view('cooperation.admin.cooperation.coordinator.connect-to-coach.index', compact('privateMessage', 'coaches'));
    }

    public function store(Cooperation $cooperation, Request $request)
    {

    }
}
