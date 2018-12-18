<?php

namespace App\Http\Controllers\Cooperation\Admin\Cooperation\Coordinator;

use App\Models\BuildingCoachStatus;
use App\Models\Cooperation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BuildingController extends Controller
{
    public function index(Cooperation $cooperation)
    {
        // coach statuses from cooperation users
//        $buildingsFromCooperation = \DB::table('cooperations')
//            ->where('cooperations.id', '=', $cooperation->id)
//            ->join('cooperation_user', 'cooperations.id', '=', 'cooperation_user.cooperation_id')
//            ->join('buildings', 'cooperation_user.user_id', '=', 'buildings.user_id')
//            ->join('users', 'buildings.user_id', '=', 'users.id')
//            ->join('building_coach_statuses', 'buildings.id', '=', 'building_coach_statuses.building_id')
//            ->leftJoin('private_messages', 'building_coach_statuses.private_message_id', '=', 'private_messages.id')
//            ->select('building_coach_statuses.*', 'private_messages.allow_access', 'users.first_name', 'users.last_name')->get();

        $residentsThatGaveAccessToBuilding =\DB::table('cooperations')
            ->where('cooperations.id', '=', $cooperation->id)
            ->join('cooperation_user', 'cooperations.id', '=', 'cooperation_user.cooperation_id')
            ->join('private_messages', function ($join) {
                $join->on('cooperation_user.user_id', '=', 'private_messages.from_user_id')
                    ->where('private_messages.allow_access', '=', true);
            })
            ->join('users', 'private_messages.from_user_id', '=', 'users.id')
            ->join('buildings', 'users.id', '=', 'buildings.user_id')
            ->select('buildings.*', 'users.first_name', 'users.last_name', 'private_messages.allow_access', 'private_messages.id as private_message_id')
            ->get();


        $buildingCoachStatuses = BuildingCoachStatus::all();

        return view('cooperation.admin.cooperation.coordinator.buildings.index', compact('residentsThatGaveAccessToBuilding', 'buildingCoachStatuses'));
    }
}
