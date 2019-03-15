<?php

namespace App\Http\Controllers\Cooperation\Admin;

use App\Http\Controllers\Controller;
use App\Models\BuildingCoachStatus;
use App\Models\Cooperation;
use App\Models\PrivateMessage;
use App\Models\User;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Handles the data for the show user for a coach, coordinator and cooperation-admin
     *
     * @param Cooperation $cooperation
     * @param $userId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Cooperation $cooperation, $userId)
    {
        $user = $cooperation->users()->find($userId);
        $building = $user->buildings()->withTrashed()->first();

        $userDoesNotExist = !$user instanceof User;
        $userExists = !$userDoesNotExist;
        $buildingId = $building->id;
        $roles = Role::all();
        $coaches = $cooperation->getCoaches()->get();

        $manageableStatuses = BuildingCoachStatus::getManageableStatuses();
        $coachesWithActiveBuildingCoachStatus = BuildingCoachStatus::getConnectedCoachesByBuildingId($buildingId);

        $mostRecentStatusesForBuildingId = BuildingCoachStatus::getMostRecentStatusesForBuildingId($buildingId);

        // if the user is a coach we can get the specific one for the current coach
        // else we just get the most recent one.
        if (\Auth::user()->hasRoleAndIsCurrentRole('coach')) {
            $mostRecentBcs = $mostRecentStatusesForBuildingId->where('coach_id', \Auth::id())->all();
        } else {
            $mostRecentBuildingCoachStatusArray = $mostRecentStatusesForBuildingId->all();
            $mostRecentBcs =[$mostRecentBuildingCoachStatusArray[0]];
        }

        // hydrate the building coach status model so it will be easier to do stuff in the views
        $mostRecentBuildingCoachStatus = BuildingCoachStatus::hydrate(
            $mostRecentBcs
        )->first();

        $privateMessages = PrivateMessage::forMyCooperation()->private()->conversation($buildingId)->get();
        $publicMessages = PrivateMessage::forMyCooperation()->public()->conversation($buildingId)->get();

        // get all the building notes
        $buildingNotes = $building->buildingNotes()->orderByDesc('updated_at')->get();

        // since a user can be deleted, a buildin
        if ($userExists) {
            // get previous user id
            $previous = $cooperation->users()->where('id', '<', $user->id)->max('id');
            // get next user id
            $next = $cooperation->users()->where('id', '>', $user->id)->min('id');
        }

        return view('cooperation.admin.users.show', compact(
                'user', 'building', 'roles', 'coaches', 'lastKnownBuildingCoachStatus', 'coachesWithActiveBuildingCoachStatus',
                'privateMessages', 'publicMessages', 'buildingNotes', 'previous', 'next', 'manageableStatuses', 'mostRecentBuildingCoachStatus',
                'userDoesNotExist', 'userExists'
            )
        );
    }
}
