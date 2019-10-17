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
use Illuminate\Support\Facades\Log;

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
        $status = Status::findOrFail($statusId);

        /** @var Building $building */
        $building = Building::withTrashed()->findOrFail($buildingId);

        $this->authorize('set-status', $building);

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

        /** @var Building $building */
        $building = Building::withTrashed()->findOrFail($buildingId);

        $this->authorize('set-appointment', $building);

        $building->setAppointmentDate(is_null($appointmentDate) ? null : Carbon::parse($appointmentDate));

    }
}
