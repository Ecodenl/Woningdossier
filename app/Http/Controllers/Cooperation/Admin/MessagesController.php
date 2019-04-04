<?php

namespace App\Http\Controllers\Cooperation\Admin;

use App\Http\Requests\Cooperation\Admin\MessageRequest;
use App\Models\Building;
use App\Models\BuildingCoachStatus;
use App\Models\Cooperation;
use App\Models\PrivateMessage;
use App\Services\MessageService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MessagesController extends Controller
{
    protected $fragment;

    public function __construct(Cooperation $coodperation, Request $request)
    {
        if ($request->has('fragment')) {
            $this->fragment = $request->get('fragment');
        }
    }

    public function index()
    {
        if (\Auth::user()->hasRoleAndIsCurrentRole('coach')) {
            $userId = \Auth::id();

            $connectedBuildingsByUserId = BuildingCoachStatus::getConnectedBuildingsByUserId($userId);
            $buildingIds = $connectedBuildingsByUserId->pluck('building_id')->all();
        } else {
            // get all the conversation requests that were send to my cooperation.
            $privateMessages = PrivateMessage::forMyCooperation()->conversationRequest()->get();
            $buildingIds = $privateMessages->pluck('building_id')->all();
        }

        $buildings = Building::withTrashed()->findMany($buildingIds);

        return view('cooperation.admin.messages.index', compact('buildings'));
    }

    /**
     * Method that handles sending messages for the /admin section
     *
     * @param Cooperation $cooperation
     * @param MessageRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function sendMessage(Cooperation $cooperation, MessageRequest $request)
    {
        MessageService::create($request);

        return redirect(back()->getTargetUrl().$this->fragment);
    }
}
