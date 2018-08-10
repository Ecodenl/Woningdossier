<?php

namespace App\Http\Controllers\Cooperation\Admin\Coach;

use App\Models\Building;
use App\Models\BuildingCoachStatus;
use App\Models\BuildingPermission;
use App\Models\Cooperation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BuildingController extends Controller
{
    public function index()
    {

        $buildingPermissions = \Auth::user()->buildingPermissions;
        $buildingCoachStatuses = BuildingCoachStatus::all();

        return view('cooperation.admin.coach.buildings.index', compact('buildingPermissions', 'buildingCoachStatuses'));
    }


    public function setBuildingStatus(Request $request)
    {
        $buildingCoachStatusId = $request->get('building_coach_status', '');
        $buildingId = $request->get('building_id');


        // TODO: implement hasPermission function when present
        if (\Auth::user()->buildingPermissions()->where('building_id', $buildingId) instanceof BuildingPermission){
            Building::find($buildingId)->update(['enum' => $buildingCoachStatusId]);
        }


    }

    /**
     * Set the sessions and after that redirect them to the tool
     *
     * @param Cooperation $cooperation
     * @param $buildingId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function fillForUser(Cooperation $cooperation, $buildingId)
    {
        $building = Building::find($buildingId);
        session(
            [
                'user_id' => \Auth::id(),
                'source_id' => 1, // TODO: get from table, if table is present
                'building_id' => $buildingId,
                'coaching' => [
                    'user_id' => $building->user->id,
                    'source_id' => 1, // same TODO as the one as above
                ],
            ]
        );

        return redirect()->route('cooperation.tool.index');
    }
}
