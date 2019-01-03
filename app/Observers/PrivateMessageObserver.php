<?php

namespace App\Observers;

use App\Models\Building;
use App\Models\BuildingCoachStatus;
use App\Models\BuildingPermission;
use App\Models\PrivateMessage;

class PrivateMessageObserver
{

    public function updating(PrivateMessage $privateMessage)
    {
        if ($privateMessage->isDirty('allow_access')) {
            // the user turned the access for his hoomdossier on
            if ($privateMessage->allow_access) {

                $senderId = $privateMessage->from_user_id;
                $buildingFromSender = Building::where('user_id', $senderId)->first();
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
                        'building_id' => $coachWithAccessToResidentBuildingStatus->building_id
                    ]);
                }
            } else if (!$privateMessage->allow_access) {
                // the user wants to revoke the access for all the connected coaches.

                $senderId = $privateMessage->from_user_id;
                $buildingFromSender = Building::where('user_id', $senderId)->first();
                $buildingId = $buildingFromSender->id;

                // delete all the building permissions for this building
                BuildingPermission::where('building_id', $buildingId)->delete();
            }
        }
    }
}