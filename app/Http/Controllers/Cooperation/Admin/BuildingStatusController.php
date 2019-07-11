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

class BuildingStatusController extends Controller
{
    /**
     * Set an status for an building
     *
     * @param Cooperation $cooperation
     *
     * @param BuildingCoachStatusRequest $request
     */
    public function setStatus(Cooperation $cooperation, BuildingCoachStatusRequest $request)
    {
        $statusId = $request->get('status_id');
        $buildingId = $request->get('building_id');
        $building = Building::withTrashed()->findOrFail($buildingId);

        $status = Status::findOrFail($statusId);

        $building->setStatus($status);
    }

    /**
     * Set an appointment date for a building
     *
     *
     * @param Cooperation $cooperation
     *
     * @param Request $request
     */
    public function setAppointmentDate(Cooperation $cooperation, Request $request)
    {
        $buildingId = $request->get('building_id');
        $appointmentDate = $request->get('appointment_date');

        $building = Building::withTrashed()->findOrFail($buildingId);

        $building->setAppointmentDate($appointmentDate);

    }
}
