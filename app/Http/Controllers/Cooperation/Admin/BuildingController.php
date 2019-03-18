<?php

namespace App\Http\Controllers\Cooperation\Admin;

use App\Http\Controllers\Controller;
use App\Models\Building;
use App\Models\BuildingCoachStatus;
use App\Models\Cooperation;
use App\Models\Log;
use App\Models\PrivateMessage;
use App\Models\User;
use Carbon\Carbon;
use Spatie\Permission\Models\Role;

class BuildingController extends Controller
{
    /**
     * Handles the data for the show user for a coach, coordinator and cooperation-admin
     *
     * @param Cooperation $cooperation
     * @param $buildingId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Cooperation $cooperation, $buildingId)
    {
        $building = Building::withTrashed()->find($buildingId);
        $user = $building->user()->first();

        $userDoesNotExist = !$user instanceof User;
        $userExists = !$userDoesNotExist;
        $buildingId = $building->id;
        $roles = Role::all();
        $coaches = $cooperation->getCoaches()->get();

        $manageableStatuses = BuildingCoachStatus::getManageableStatuses();
        $coachesWithActiveBuildingCoachStatus = BuildingCoachStatus::getConnectedCoachesByBuildingId($buildingId);

        $mostRecentStatusesForBuildingId = BuildingCoachStatus::getMostRecentStatusesForBuildingId($buildingId);

        $mostRecentBcs = [];
        // first check if there are any.
        if ($mostRecentStatusesForBuildingId->isNotEmpty()) {
            // if the user is a coach we can get the specific one for the current coach
            // else we just get the most recent one.
            if (\Auth::user()->hasRoleAndIsCurrentRole('coach')) {
                $mostRecentBcs = $mostRecentStatusesForBuildingId->where('coach_id', \Auth::id())->all();
            } else {
                $mostRecentBuildingCoachStatusArray = $mostRecentStatusesForBuildingId->all();
                $mostRecentBcs = [$mostRecentBuildingCoachStatusArray[0]];
            }
        }


        // hydrate the building coach status model so it will be easier to do stuff in the views
        $mostRecentBuildingCoachStatus = BuildingCoachStatus::hydrate(
            $mostRecentBcs
        )->first();

        $logs = Log::forBuildingId($buildingId)->get();

        $privateMessages = PrivateMessage::forMyCooperation()->private()->conversation($buildingId)->get();
        $publicMessages = PrivateMessage::forMyCooperation()->public()->conversation($buildingId)->get();

        // get all the building notes
        $buildingNotes = $building->buildingNotes()->orderByDesc('updated_at')->get();

        // since a user can be deleted, a buildin
        if ($userExists) {
            // get previous user id
            $previous = $building->where('id', '<', $buildingId)->max('id');
            // get next user id
            $next = $building->where('id', '>', $buildingId)->min('id');
        }

        return view('cooperation.admin.buildings.show', compact(
                'user', 'building', 'roles', 'coaches', 'lastKnownBuildingCoachStatus', 'coachesWithActiveBuildingCoachStatus',
                'privateMessages', 'publicMessages', 'buildingNotes', 'previous', 'next', 'manageableStatuses', 'mostRecentBuildingCoachStatus',
                'userDoesNotExist', 'userExists', 'logs'
            )
        );
    }
}
