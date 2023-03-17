<?php

namespace App\Http\Controllers\Cooperation\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cooperation\Admin\BuildingCoachStatusRequest;
use App\Models\Building;
use App\Models\Cooperation;
use App\Models\Status;
use App\Services\Models\BuildingService;
use App\Services\Models\BuildingStatusService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BuildingStatusController extends Controller
{
    /**
     * Set an status for an building.
     */
    public function setStatus(BuildingStatusService $buildingStatusService, Cooperation $cooperation, BuildingCoachStatusRequest $request)
    {
        $statusId = $request->get('status_id');
        $buildingId = $request->get('building_id');
        $status = Status::findOrFail($statusId);

        /** @var Building $building */
        $building = Building::withTrashed()->findOrFail($buildingId);

        $this->authorize('set-status', $building);

        $buildingStatusService->forBuilding($building)->setStatus($status);

    }

    /**
     * Set an appointment date for a building.
     */
    public function setAppointmentDate(Cooperation $cooperation, Request $request)
    {
        $buildingId = $request->get('building_id');
        $appointmentDate = $request->get('appointment_date');

        /** @var Building $building */
        $building = Building::withTrashed()->findOrFail($buildingId);

        $this->authorize('set-appointment', $building);

        app(BuildingService::class, compact('building'))
            ->setAppointmentDate(is_null($appointmentDate) ? null : Carbon::parse($appointmentDate));
    }
}
