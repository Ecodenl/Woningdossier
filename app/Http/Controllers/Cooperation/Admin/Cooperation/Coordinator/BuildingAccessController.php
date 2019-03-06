<?php

namespace App\Http\Controllers\Cooperation\Admin\Cooperation\Coordinator;

use App\Http\Controllers\Controller;
use App\Models\Building;
use App\Models\BuildingPermission;
use App\Models\Cooperation;
use App\Models\PrivateMessage;
use Illuminate\Http\Request;

class BuildingAccessController extends Controller
{
    public function index(Cooperation $cooperation)
    {
        // we want to show the coordinators all the buildings that initiated a requested to the cooperation.
        $privateMessageBuildingIds = PrivateMessage::forMyCooperation()
            ->accessAllowed()
            ->groupBy('building_id')
            ->select('building_id')
            ->get()
            ->toArray();

        $flattenedBuildingIds = array_flatten($privateMessageBuildingIds);

        $buildings = Building::findMany($flattenedBuildingIds);

        return view('cooperation.admin.cooperation.coordinator.building-access.index', compact('buildings'));
    }

    public function manageConnectedCoaches(Cooperation $cooperation, $buildingId)
    {
        return redirect()
            ->route('cooperation.admin.cooperation.coordinator.messages.public.edit', ['buildingId' => $buildingId])
            ->with('success', __('woningdossier.cooperation.admin.cooperation.coordinator.building-access.manage-connected-coaches.redirect-message'));
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
