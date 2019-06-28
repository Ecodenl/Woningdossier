<?php

namespace App\Http\Controllers\Cooperation\Admin;

use App\Helpers\Hoomdossier;
use App\Http\Requests\Cooperation\Admin\BuildingCoachStatusRequest;
use App\Models\Building;
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
     *
     */
    public function setStatus(Cooperation $cooperation, BuildingCoachStatusRequest $request)
    {
        $status = $request->get('status');
        $buildingId = $request->get('building_id');
        $building = Building::withTrashed()->find($buildingId);

        // we only want to set it for the coaches that are currently 'active'
        $connectedCoachesToBuilding = BuildingCoachStatus::getConnectedCoachesByBuildingId($buildingId);

        // retrieve the most recent statuses for each coach that is active on the building
        $mostRecentBuildingCoachStatuses = BuildingCoachStatus::getMostRecentStatusesForBuildingId($buildingId);


        // if the in active status get chosen, we will set the building status to inactive.
        // we wont do anything will the building_coach_status at this point.
        // else we change the building status itself to active
        // and apply the chosen building coach status.
        if ($status == Building::STATUS_IS_NOT_ACTIVE) {
            $building->status = Building::STATUS_IS_NOT_ACTIVE;

            // we need to copy the most recent status and see if it has an appointment date.
            // if it has we need to create a new row without it otherwise the building is inactive and the appointment date is still set.
            foreach ($mostRecentBuildingCoachStatuses as $mostRecentBuildingCoachStatus) {
                if (!is_null($mostRecentBuildingCoachStatus->appointment_date)) {
                    BuildingCoachStatus::create([
                        'coach_id' => $mostRecentBuildingCoachStatus->coach_id,
                        'building_id' => $mostRecentBuildingCoachStatus->building_id,
                        'status' => $mostRecentBuildingCoachStatus->status,
                    ]);
                }
            }

        } else {
            $building->status = Building::STATUS_IS_ACTIVE;
            // if the user is a coach, then he may only set the status for himself
            // if the user is a coordinator or cooperation-admin we set the status for every connected coach
            if (\App\Helpers\Hoomdossier::user()->hasRoleAndIsCurrentRole(['coach'])) {

                $createData = [
                    'coach_id' => Hoomdossier::user()->id,
                    'building_id' => $buildingId,
                    'status' => $status,
                ];

                if ($status == BuildingCoachStatus::STATUS_EXECUTED) {
                    $mostRecentBuildingCoachStatus = $mostRecentBuildingCoachStatuses->where('coach_id', \Auth::id())->first();
                    $createData['appointment_date'] = $mostRecentBuildingCoachStatus->appointment_date;
                }

                // now create the new status for all the coaches
                BuildingCoachStatus::create($createData);
            } else {

                foreach ($connectedCoachesToBuilding as $connectedCoachToBuilding) {
                    $createData = [
                        'coach_id' => $connectedCoachToBuilding->coach_id,
                        'building_id' => $buildingId,
                        'status' => $status
                    ];
                    if ($status == BuildingCoachStatus::STATUS_EXECUTED) {
                        $mostRecentBuildingCoachStatus = $mostRecentBuildingCoachStatuses->first();
                        $createData['appointment_date'] = $mostRecentBuildingCoachStatus->appointment_date;
                    }
                    // now create the new status for all the coaches
                    BuildingCoachStatus::create($createData);
                }
            }
        }
        $building->save();
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
        if (\App\Helpers\Hoomdossier::user()->hasRoleAndIsCurrentRole(['coach'])) {
            $mostRecentBuildingCoachStatus = $mostRecentBuildingCoachStatuses->where('coach_id', \Auth::id())->first();

            // now create the new status for all the coaches
            BuildingCoachStatus::create([
                'coach_id' => Hoomdossier::user()->id,
                'building_id' => $buildingId,
                'status' => $this->getStatusToSetForAppointment($mostRecentBuildingCoachStatus, $appointmentDate),
                'appointment_date' => $appointmentDate,
            ]);
        } else if (\App\Helpers\Hoomdossier::user()->hasRoleAndIsCurrentRole(['coordinator', 'cooperation-admin'])) {
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
