<?php

namespace App\Http\Controllers\Cooperation\Admin;

use App\Http\Requests\Cooperation\Admin\BuildingCoachStatusRequest;
use App\Models\BuildingCoachStatus;
use App\Models\Cooperation;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;

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

        // if the user is a coach, then he may only set the status for himself
        // if the user is a coordinator or cooperation-admin we set the status for every connected coach
        if (\Auth::user()->hasRoleAndIsCurrentRole(['coach'])) {
            // now create the new status for all the coaches
            BuildingCoachStatus::create([
                'coach_id' => \Auth::id(),
                'building_id' => $buildingId,
                'status' => $status
            ]);
        } else {
            foreach ($connectedCoachesToBuilding as $connectedCoachToBuilding) {
                // now create the new status for all the coaches
                BuildingCoachStatus::create([
                    'coach_id' => $connectedCoachToBuilding->coach_id,
                    'building_id' => $buildingId,
                    'status' => $status
                ]);
            }
        }
    }

    /**
     * Set a appointment date for a building id, we will set this for all the permitted coaches on the building.
     * We get the most recent building status and will use that as status for the appointment date
     *
     * @param Cooperation $cooperation
     * @param Request $request
     */
    public function setAppointmentDate(Cooperation $cooperation, Request $request)
    {
        $buildingId = $request->get('building_id');
        $appointmentDate = $request->get('appointment_date');

        if (!is_null($appointmentDate)) {
            $appointmentDate = Carbon::parse($appointmentDate);
        }

        $mostRecentBuildingCoachStatuses = BuildingCoachStatus::getMostRecentStatusesForBuildingId($buildingId);

        // we only want to set it for the coaches that are currently 'active'
        $connectedCoachesToBuilding = BuildingCoachStatus::getConnectedCoachesByBuildingId($buildingId);

        // if the user is a coach, then he may only set the appointment date forhimself
        // if the user is a coordinator or cooperation-admin we set the building coach statuses for every connected active coach
        if (\Auth::user()->hasRoleAndIsCurrentRole(['coach'])) {
            $mostRecentBuildingCoachStatus = $mostRecentBuildingCoachStatuses->where('coach_id', \Auth::id())->first();

            // now create the new status for all the coaches
            BuildingCoachStatus::create([
                'coach_id' => \Auth::id(),
                'building_id' => $buildingId,
                'status' => $this->getStatusToSetForAppointment($mostRecentBuildingCoachStatus, $appointmentDate),
                'appointment_date' => $appointmentDate,
            ]);
        } else if (\Auth::user()->hasRoleAndIsCurrentRole(['coordinator', 'cooperation-admin'])) {
            $mostRecentBuildingCoachStatus = $mostRecentBuildingCoachStatuses->first();
            foreach ($connectedCoachesToBuilding as $connectedCoachToBuilding) {
                // now create the new status for all the coaches
                BuildingCoachStatus::create([
                    'coach_id' => $connectedCoachToBuilding->coach_id,
                    'building_id' => $connectedCoachToBuilding->building_id,
                    'status' => $this->getStatusToSetForAppointment($mostRecentBuildingCoachStatus, $appointmentDate),
                    'appointment_date' => $appointmentDate
                ]);
            }
        }
    }

    /**
     * Get the right status to set for an appointment.
     *
     * @param $mostRecentBuildingCoachStatus
     * @return string
     */
    public function getStatusToSetForAppointment($mostRecentBuildingCoachStatus, $appointmentDate): string
    {
        // if the appointment date is set to null, we set the status to no execution.
        if (!is_null($appointmentDate)) {
            // if a coach tries to make a appointment date while the status is still set to pending, then we set the status to in progress ourself
            if ($mostRecentBuildingCoachStatus->status == BuildingCoachStatus::STATUS_PENDING) {
                $status = BuildingCoachStatus::STATUS_IN_PROGRESS;
            } else {
                $status = $mostRecentBuildingCoachStatus->status;
            }
        } else {
            $status = BuildingCoachStatus::STATUS_NO_EXECUTION;
        }
        return $status;
    }
}
