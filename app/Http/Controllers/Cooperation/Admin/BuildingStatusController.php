<?php

namespace App\Http\Controllers\Cooperation\Admin;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cooperation\Admin\BuildingCoachStatusRequest;
use App\Models\Building;
use App\Models\Cooperation;
use App\Models\Status;
use App\Services\Models\BuildingService;
use App\Services\Models\BuildingStatusService;
use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;
use Illuminate\Http\Request;

class BuildingStatusController extends Controller
{
    /**
     * Set an status for an building.
     */
    public function setStatus(BuildingStatusService $buildingStatusService, Cooperation $cooperation, BuildingCoachStatusRequest $request)
    {
        $statusId = $request->input('status_id');
        $buildingId = $request->input('building_id');
        $status = Status::findOrFail($statusId);

        /** @var Building $building */
        $building = Building::withTrashed()->findOrFail($buildingId);

        $this->authorize('set-status', $building);

        $buildingStatusService->forBuilding($building)->setStatus($status);
    }

    /**
     * Set an appointment date for a building.
     */
    public function setAppointmentDate(Cooperation $cooperation, Request $request): JsonResponse
    {
        $buildingId = $request->input('building_id');
        $appointmentDate = $request->input('appointment_date');

        if (! is_null($appointmentDate)) {
            try {
                $appointmentDate = Carbon::parse($appointmentDate);
            } catch (InvalidFormatException $e) {
                // Invalid date given; we will try the format that has basically thrown all exceptions:
                try {
                    $appointmentDate = Carbon::createFromFormat('d-m-Y H', $appointmentDate);
                } catch (InvalidFormatException $e) {
                    // Now we could keep trying different formats, but if this happens it's basically because the end
                    // user hasn't properly used the datepicker, so we will just redirect back and tell them to
                    // use the datepicker.

                    return response()->json("Invalid format", 422);
                }
            }
        }

        /** @var Building $building */
        $building = Building::withTrashed()->findOrFail($buildingId);

        $this->authorize('set-appointment', $building);

        app(BuildingService::class, compact('building'))
            ->setAppointmentDate($appointmentDate);
    }
}
