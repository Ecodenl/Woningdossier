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
        $buildingCoachStatus = $request->get('building_coach_status', '');
        $buildingId = $request->get('building_id');


        if (\Auth::user()->buildingPermissions()->where('building_id', $buildingId)->first() instanceof BuildingPermission) {

            BuildingCoachStatus::updateOrCreate(
                [
                    'coach_id' => \Auth::id(),
                    'building_id' => $buildingId
                ],
                [
                    'status' => $buildingCoachStatus,
                ]
            );
        }

        return redirect()->route('cooperation.admin.coach.buildings.index')->with('success', __('woningdossier.cooperation.admin.coach.buildings.set-building-status.success'));

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

        // makes no sense at all, rewrite when input_sources branch is merged
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
