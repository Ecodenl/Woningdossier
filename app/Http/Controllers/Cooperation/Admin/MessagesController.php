<?php

namespace App\Http\Controllers\Cooperation\Admin;

use App\Helpers\Hoomdossier;
use App\Http\Requests\Cooperation\Admin\MessageRequest;
use App\Models\Building;
use App\Models\BuildingCoachStatus;
use App\Models\Cooperation;
use App\Models\PrivateMessage;
use App\Services\MessageService;
use App\Http\Controllers\Controller;
use App\Services\PrivateMessageService;

class MessagesController extends Controller
{
    public function index(Cooperation $cooperation)
    {

        if (Hoomdossier::user()->hasRoleAndIsCurrentRole('coach')) {
            $connectedBuildingsByUserId = BuildingCoachStatus::getConnectedBuildingsByUser(Hoomdossier::user(), $cooperation);
            $buildingIds                = $connectedBuildingsByUserId->pluck('building_id')->all();
        } else {
            $privateMessages = PrivateMessage::where('to_cooperation_id', $cooperation->id)
                ->conversationRequest()
                ->get();

            $buildingIds     = $privateMessages->pluck('building_id')->all();
        }

        $buildings = Building::whereHas('privateMessages')->findMany($buildingIds);

        return view('cooperation.admin.messages.index', compact('buildings'));
    }

    /**
     * Method that handles sending messages for the /admin section
     *
     * @param  Cooperation     $cooperation
     * @param  MessageRequest  $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function sendMessage(Cooperation $cooperation, MessageRequest $request)
    {
        PrivateMessageService::create($request);

        return redirect()->back()->with('fragment', $request->get('fragment'));
    }
}
