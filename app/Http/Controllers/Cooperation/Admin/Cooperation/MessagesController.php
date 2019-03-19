<?php

namespace App\Http\Controllers\Cooperation\Admin\Cooperation;

use App\Models\Building;
use App\Models\PrivateMessage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MessagesController extends Controller
{
    public function index()
    {
        // get all the conversation requests that were send to my cooperation.
        $privateMessages = PrivateMessage::forMyCooperation()->conversationRequest()->get();
        $buildingIds = $privateMessages->pluck('building_id')->all();

        $buildings = Building::withTrashed()->findMany($buildingIds);

        return view('cooperation.admin.cooperation.messages.index', compact('buildings'));
    }
}
