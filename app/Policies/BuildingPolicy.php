<?php

namespace App\Policies;

use App\Models\Building;
use App\Models\BuildingCoachStatus;
use App\Models\BuildingPermission;
use App\Models\Cooperation;
use App\Models\PrivateMessage;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BuildingPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {

    }


    /**
     * Determine if a user is allowed to see a building overview
     *
     *
     * @param  User  $user
     * @param  Building  $building
     *
     * @return bool
     */
    public function show(User $user, Building $building, Cooperation $cooperation)
    {
        if ($user->hasRoleAndIsCurrentRole('coach')) {
            // get the buildings the user is connected to.
            $connectedBuildingsForUser = BuildingCoachStatus::getConnectedBuildingsByUser($user, $cooperation);

            // check if the current building is in that collection.
            return  $connectedBuildingsForUser->contains('building_id', $building->id);
        }

        // they can always view a building.
        return  $user->hasRoleAndIsCurrentRole(['coordinator', 'cooperation-admin']);
    }


    /**
     * Determine if its possible / authorized to talk to a resident
     *
     * Its possible when there is 1 public message from the resident itself.
     *
     * @param  User  $user
     * @param  Building  $building
     *
     * @return bool
     */
    public function talkToResident(User $user, Building $building, Cooperation $cooperation)
    {
        if ($user->hasRoleAndIsCurrentRole('coach')) {

            // get the buildings the user is connected to.
            $connectedBuildingsForUser = BuildingCoachStatus::getConnectedBuildingsByUser($user, $cooperation);

            // check if the current building is in that collection and if there are public messages.
            return  $connectedBuildingsForUser->contains('building_id', $building->id) && $building->privateMessages()->public()->first() instanceof PrivateMessage;
        }

        return  $user->hasRoleAndIsCurrentRole(['coordinator', 'cooperation-admin']) && $building->privateMessages()->public()->first() instanceof PrivateMessage;
    }


    /**
     * Determine if a user is allowed to view the building info
     *
     * With building info we mean stuff like associate coaches, make appointments etc.
     * This is authorized when a user gave access in the conversation request / the allow_access is set to true
     *
     * @param  User  $user
     * @param  Building  $building
     *
     * @return bool
     */
    public function viewBuildingInfo(User $user, Building $building): bool
    {
        return PrivateMessage::allowedAccess($building->id);
    }

    /**
     * Determine if a user can access his building
     *
     * With access we mean observing / filling the tool.
     *
     * @param User $user
     * @param Building $building
     * @return bool
     */
    public function accessBuilding(User $user, Building $building): bool
    {

        if ($user->hasRoleAndIsCurrentRole('coach')) {

            // check if the coach has building permission
            $coachHasBuildingPermission = Building::withTrashed()->find($building->id)->buildingPermissions()->where('user_id', $user->id)->first() instanceof BuildingPermission;

            return  PrivateMessage::allowedAccess($building->id) && $coachHasBuildingPermission;
        }

        // they can always access a building (if the user / resident gave access)
        return  PrivateMessage::allowedAccess($building->id) && $user->hasRoleAndIsCurrentRole(['coordinator', 'cooperation-admin']);
    }
}
