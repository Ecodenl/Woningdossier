<?php

namespace App\Http\Controllers\Cooperation\Admin\Cooperation\Coordinator;

use App\Helpers\HoomdossierSession;
use App\Http\Controllers\Controller;
use App\Models\Building;
use App\Models\BuildingCoachStatus;
use App\Models\BuildingPermission;
use App\Models\Cooperation;
use App\Models\PrivateMessage;
use App\Models\User;
use Illuminate\Http\Request;

class BuildingAccessController extends Controller
{
    public function index(Cooperation $cooperation)
    {
//        // i apologize for this variable naming.
//        $results = \DB::table('cooperations')
//            ->where('cooperations.id', '=', $cooperation->id)
//            ->join('cooperation_user', 'cooperations.id', '=', 'cooperation_user.cooperation_id')
//            ->join('private_messages', function ($join) {
//                $join->on('cooperation_user.user_id', '=', 'private_messages.from_user_id');
//            })
//            ->join('users', 'private_messages.from_user_id', '=', 'users.id')
//            ->join('buildings', 'users.id', '=', 'buildings.user_id')
//            ->select('buildings.*', 'users.first_name', 'users.last_name', 'private_messages.allow_access', 'private_messages.id as private_message_id')
//            ->get();
//
//        // filter the result set
//        $filteredResults = $results->filter(function ($result) use ($results) {
//            // i dont know how to name this variable at all.
//            $residents = $results->where('user_id', $result->user_id);
//
//            // if the user submitted multiple conversation requests and his request contains a allow access that is set to true;
//            // return that one
//            // else we return them all and unique the collection on user id, it does not matter which one we return
//            if ($residents->count() > 1 && $residents->contains('allow_access', true)) {
//                return $result->allow_access;
//            } else {
//                return $result;
//            }
//        })->unique('user_id');


        // we want to show the coordinators all the buildings that initiated a requested to the cooperation.
        $privateMessageBuildingIds = PrivateMessage::where('to_cooperation_id', HoomdossierSession::getCooperation())
            ->groupBy('building_id')
            ->select('building_id')
            ->get()
            ->toArray();

        $flattenedBuildingIds = array_flatten($privateMessageBuildingIds);

        $buildings = Building::findMany($flattenedBuildingIds);

        $buildingCoachStatuses = BuildingCoachStatus::all();

        return view('cooperation.admin.cooperation.coordinator.building-access.index', compact('buildings', 'buildingCoachStatuses'));
    }

    public function edit(Cooperation $cooperation, $buildingId)
    {
        $building = Building::find($buildingId);

        $usersThatHaveAccessToBuilding = \DB::table('building_permissions')
            ->where('building_id', '=', $buildingId)
            ->join('users', 'building_permissions.user_id', '=', 'users.id')
            ->select('users.*')
            ->get();

        return view('cooperation.admin.cooperation.coordinator.building-access.edit', compact('building', 'usersThatHaveAccessToBuilding'));
    }

    public function destroy(Cooperation $cooperation, Request $request)
    {
        $buildingId = $request->get('building_id');
        $userId = $request->get('user_id');

        BuildingPermission::where('building_id', $buildingId)->where('user_id', $userId)->delete();

        return redirect()->back()->with('success', __('woningdossier.cooperation.admin.cooperation.coordinator.building-access.destroy.success'));
    }
}
