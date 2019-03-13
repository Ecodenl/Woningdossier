<?php

namespace App\Http\Controllers\Cooperation\Admin;

use App\Http\Requests\Cooperation\Admin\BuildingCoachStatusRequest;
use App\Models\BuildingCoachStatus;
use App\Models\Cooperation;
use App\Http\Controllers\Controller;

class BuildingCoachStatusController extends Controller
{
    /**
     * Set a status for a building id
     *
     * @param Cooperation $cooperation
     * @param BuildingCoachStatusRequest $request
     */
    public function setStatus(Cooperation $cooperation, BuildingCoachStatusRequest $request)
    {
        $status = $request->get('status');
        $buildingId = $request->get('building_id');

        // we only want to set it for the coaches that are currently 'active'
        $connectedCoachesToBuilding = BuildingCoachStatus::getConnectedCoachesByBuildingId($buildingId);

        foreach ($connectedCoachesToBuilding as $connectedCoachToBuilding) {
            // now create the new status for all the coaches
            BuildingCoachStatus::create([
                'coach_id' => $connectedCoachToBuilding->coach_id,
                'building_id' => $connectedCoachToBuilding->building_id,
                'status' => $status
            ]);
        }
    }
}
