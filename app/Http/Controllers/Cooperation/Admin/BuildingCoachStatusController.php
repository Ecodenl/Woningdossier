<?php

namespace App\Http\Controllers\Cooperation\Admin;

use App\Http\Requests\Cooperation\Admin\BuildingCoachStatusRequest;
use App\Models\Building;
use App\Models\BuildingCoachStatus;
use App\Models\BuildingStatus;
use App\Models\Cooperation;
use App\Http\Controllers\Controller;
use App\Models\Status;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * Class BuildingCoachStatusController
 *
 * @note: controller is not used at the moment.
 *
 * @package App\Http\Controllers\Cooperation\Admin
 */
class BuildingCoachStatusController extends Controller
{
    /**
     * Set a status for a building id
     *
     * @param Cooperation $cooperation
     * @param BuildingCoachStatusRequest $request
     *
     */
    public function setStatus(Cooperation $cooperation, BuildingCoachStatusRequest $request)
    {
        $statusId = $request->get('status_id');
        $buildingId = $request->get('building_id');
        $building = Building::withTrashed()->find($buildingId);

        $mostRecentBuildingStatus = $building->getMostRecentStatus();

        $status = Status::findOrFail($statusId);

        BuildingStatus::create([
            'building_id' => $buildingId,
            'status_id' => $status->id,
            'appointment_date' => $mostRecentBuildingStatus->appointment_date,
        ]);

        $building->save();
    }

    /**
     * Set a appointment date for a building id, we will set this for all the permitted coaches on the building.
     * We get the most recent building status and will use that as status for the appointment date
     *
     * @param Cooperation $cooperation
     *
     * @param Request $request
     */
    public function setAppointmentDate(Cooperation $cooperation, Request $request)
    {
        $buildingId = $request->get('building_id');
        $appointmentDate = $request->get('appointment_date');

        $building = Building::findOrFail($buildingId);

        $mostRecentBuildingStatus = $building->getMostRecentStatus();

        BuildingStatus::create([
            'building_id' => $buildingId,
            'status_id' => $mostRecentBuildingStatus->status_id,
            'appointment_date' => Carbon::parse($appointmentDate),
        ]);

    }
}
