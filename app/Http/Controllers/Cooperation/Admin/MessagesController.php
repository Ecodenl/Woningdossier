<?php

namespace App\Http\Controllers\Cooperation\Admin;

use App\Helpers\Hoomdossier;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cooperation\Admin\MessageRequest;
use App\Models\Building;
use App\Models\BuildingCoachStatus;
use App\Models\Cooperation;
use App\Services\PrivateMessageService;
use \Illuminate\Database\Query\Builder;

class MessagesController extends Controller
{
    public function index(Cooperation $cooperation)
    {
        if (Hoomdossier::user()->hasRoleAndIsCurrentRole('coach')) {
            $connectedBuildingsByUserId = BuildingCoachStatus::getConnectedBuildingsByUser(Hoomdossier::user(), $cooperation);

            $buildings = Building::whereHas('privateMessages')->findMany(
                $connectedBuildingsByUserId->pluck('building_id')->all()
            );
        } else {
            // 'safest' method of retrieving the buildings for each message
            // retrieve all the buildings, for the current cooperation that have a private message
            $buildings = Building::hydrate($cooperation
                ->users()
                ->select('buildings.*')
                ->join('buildings', 'users.id', 'buildings.user_id')
                ->whereExists(function (Builder $query) {
                    $query->select('*')
                        ->from('private_messages')
                        ->whereRaw('buildings.id = private_messages.building_id');
                })->get()->toArray()
            )->load(['privateMessages', 'user']);

        }

        return view('cooperation.admin.messages.index', compact('buildings'));
    }

    /**
     * Method that handles sending messages for the /admin section.
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function sendMessage(Cooperation $cooperation, MessageRequest $request)
    {
        PrivateMessageService::create($request);

        return redirect()->back()->with('fragment', $request->get('fragment'));
    }
}
