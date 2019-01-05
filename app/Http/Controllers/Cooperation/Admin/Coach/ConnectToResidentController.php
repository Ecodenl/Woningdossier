<?php

namespace App\Http\Controllers\Cooperation\Admin\Coach;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cooperation\Admin\Coach\ConnectToResidentRequest;
use App\Models\Building;
use App\Models\BuildingCoachStatus;
use App\Models\Cooperation;
use App\Models\PrivateMessage;
use App\Models\User;

class ConnectToResidentController extends Controller
{
    public function index(Cooperation $cooperation)
    {
        $buildingsFromBuildingCoachStatuses = \DB::table('building_coach_statuses')
            ->where('building_coach_statuses.coach_id', '=', \Auth::id())
            ->leftJoin('buildings', 'buildings.id', '=', 'building_coach_statuses.building_id')
            ->select('buildings.*')
            ->get()->unique('id');

        foreach ($buildingsFromBuildingCoachStatuses as $key => $building) {
            // the coach can talk to a resident if there is a coach status where the active status is higher then the deleted status
            $buildingCoachStatusActive = BuildingCoachStatus::where('building_coach_statuses.coach_id', '=', \Auth::id())
                ->where('building_id', '=', $building->id)
                ->where('status', '=', BuildingCoachStatus::STATUS_ACTIVE)->count();

            $buildingCoachStatusRemoved = BuildingCoachStatus::where('building_coach_statuses.coach_id', '=', \Auth::id())
                ->where('building_id', '=', $building->id)
                ->where('status', '=', BuildingCoachStatus::STATUS_REMOVED)->count();

            // if there are as many OR more records with removed remove it from the the collection
            // the coach does not have access to that building
            if ($buildingCoachStatusRemoved >= $buildingCoachStatusActive) {
                $buildingsFromBuildingCoachStatuses->forget($key);
            }
        }

//        $users = \DB::table('building_coach_statuses')
//            ->where('building_coach_statuses.coach_id', '=', \Auth::id())
//            ->leftJoin('buildings', 'buildings.id', '=', 'building_coach_statuses.building_id')
//            ->leftJoin('users', 'users.id', '=', 'buildings.user_id')
//            ->select('buildings.*', 'users.first_name', 'users.last_name')
//            ->get()->unique('id');

        return view('cooperation.admin.coach.connect-to-resident.index', compact('cooperation', 'buildingsFromBuildingCoachStatuses'));
    }

    public function create(Cooperation $cooperation, $userId)
    {
        $receiver = $cooperation->getResidents()->find($userId);

        if ($receiver instanceof User) {
            return view('cooperation.admin.coach.connect-to-resident.create', compact('cooperation', 'receiver', 'typeId'));
        }

        return redirect()->route('cooperation.admin.coach.connect-to-resident.index');
    }

    public function store(Cooperation $cooperation, ConnectToResidentRequest $request)
    {
        $title = $request->get('title', '');
        $message = $request->get('message', '');
        $receiverId = $request->get('receiver_id', '');

        // we start the conversation between the resident and coach
        $newMessage = PrivateMessage::create(
            [
                'title' => $title,
                'message' => $message,
                'from_user_id' => \Auth::id(),
                'to_user_id' => $receiverId,
            ]
        );

        return redirect()->route('cooperation.admin.coach.messages.edit', ['messageId' => $newMessage->id]);
    }
}
