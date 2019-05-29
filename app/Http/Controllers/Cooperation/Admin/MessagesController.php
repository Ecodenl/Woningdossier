<?php

namespace App\Http\Controllers\Cooperation\Admin;

use App\Http\Requests\Cooperation\Admin\MessageRequest;
use App\Models\Building;
use App\Models\BuildingCoachStatus;
use App\Models\Cooperation;
use App\Models\InputSource;
use App\Models\PrivateMessage;
use App\Services\MessageService;
use DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Log;
use stdClass;

class MessagesController extends Controller
{
    protected $fragment;

    public function __construct(Cooperation $coodperation, Request $request)
    {
        if ($request->has('fragment')) {
            $this->fragment = $request->get('fragment');
        }
    }

    public function index(Cooperation $cooperation)
    {

        if (\Auth::user()->hasRoleAndIsCurrentRole('coach')) {
            $connectedBuildingsByUserId = BuildingCoachStatus::getConnectedBuildingsByUser(\Auth::user(), $cooperation);
            $buildingIds                = $connectedBuildingsByUserId->pluck('building_id')->all();
        } else {
            // get all the conversation requests that were send to my cooperation.
            $privateMessages = PrivateMessage::forMyCooperation()->conversationRequest()->get();
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
        MessageService::create($request);

        return redirect(back()->getTargetUrl().$this->fragment);
    }
}
