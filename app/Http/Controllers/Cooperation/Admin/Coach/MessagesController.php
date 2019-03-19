<?php

namespace App\Http\Controllers\Cooperation\Admin\Coach;

use App\Models\BuildingCoachStatus;
use App\Models\PrivateMessage;
use App\Models\PrivateMessageView;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MessagesController extends Controller
{
    /**
     * Show a listing of connected buildings for the user, its almost no different then the buildings/index
     * But we need to show the unread messages.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $userId = \Auth::id();

        $connectedBuildingsByUserId = BuildingCoachStatus::getConnectedBuildingsByUserId($userId);

        $buildingCoachStatuses = BuildingCoachStatus::hydrate($connectedBuildingsByUserId->all());

        dd(PrivateMessageView::getTotalUnreadMessagesCountForUserAndBuildingId($userId, $buildingCoachStatuses->first()->building_id));

        return view('cooperation.admin.coach.messages.index', compact('buildingCoachStatuses'));
    }
}
