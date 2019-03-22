<?php

namespace App\Observers;

use App\Helpers\HoomdossierSession;
use App\Models\Building;
use App\Models\BuildingCoachStatus;
use App\Models\BuildingPermission;
use App\Models\PrivateMessage;
use App\Services\PrivateMessageViewService;

class PrivateMessageObserver
{
    /**
     * On updating check if the allow access is dirty, if so we need to change permissions and building_coach_statuses.
     *
     * @param PrivateMessage $privateMessage
     *
     * @throws \Exception
     */
    public function updating(PrivateMessage $privateMessage)
    {
        if ($privateMessage->isDirty('allow_access')) {
            // the user turned the access for his hoomdossier on
            if ($privateMessage->allow_access) {
                $buildingFromSender = Building::find($privateMessage->building_id);
                $buildingId = $buildingFromSender->id;

                // all the coach statuses for his building
                $buildingCoachStatuses = BuildingCoachStatus::where('building_id', $buildingId)->get();
                // unique the results on coach id.
                $uniqueBuildingCoachStatuses = $buildingCoachStatuses->unique('coach_id');

                // check if the coach has permission to talk to a resident
                foreach ($uniqueBuildingCoachStatuses as $key => $buildingCoachStatus) {
                    // the coach can talk to a resident if there is a coach status where the active status is higher then the deleted status
                    $buildingCoachStatusActive = BuildingCoachStatus::where('coach_id', '=', $buildingCoachStatus->coach_id)
                        ->where('building_id', '=', $buildingId)
                        ->where('status', '=', BuildingCoachStatus::STATUS_ACTIVE)->count();

                    $buildingCoachStatusRemoved = BuildingCoachStatus::where('coach_id', '=', $buildingCoachStatus->coach_id)
                        ->where('building_id', '=', $buildingId)
                        ->where('status', '=', BuildingCoachStatus::STATUS_REMOVED)->count();

                    // if there are as many OR more records with removed remove it from the the collection
                    // the coach does not have access to that building
                    if ($buildingCoachStatusRemoved >= $buildingCoachStatusActive) {
                        // we remove the building coach status that the user already removed in the past
                        $uniqueBuildingCoachStatuses->forget($key);
                    }
                }

                $coachesWithAccessToResidentBuildingStatuses = $uniqueBuildingCoachStatuses;

                // we give the coaches that have "permission" to talk to a resident the permissions to access the building from the resident.
                foreach ($coachesWithAccessToResidentBuildingStatuses as $coachWithAccessToResidentBuildingStatus) {
                    BuildingPermission::create([
                        'user_id' => $coachWithAccessToResidentBuildingStatus->coach_id,
                        'building_id' => $coachWithAccessToResidentBuildingStatus->building_id,
                    ]);
                }
            } elseif ($privateMessage->allow_access == false) {
                // the user wants to revoke the access for all the connected coaches.


                // delete all the building permissions for this building
                BuildingPermission::where('building_id', HoomdossierSession::getBuilding())->delete();
            }
        }
    }

    /**
     * For every message that is created we want to create a row in the private_message_view.
     *
     * @param PrivateMessage $privateMessage
     */
    public function created(PrivateMessage $privateMessage)
    {
        PrivateMessageViewService::create($privateMessage);
    }
}
