<?php

namespace App\Policies;

use App\Models\Building;
use App\Models\BuildingCoachStatus;
use App\Models\BuildingPermission;
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
    public function show(User $user, Building $building)
    {
        if ($user->hasRoleAndIsCurrentRole('coach')) {
            // get the buildings the user is connected to.
            $connectedBuildingsForUser = BuildingCoachStatus::getConnectedBuildingsByUserId($user->id);

            // check if the current building is in that collection.
            return (bool) $connectedBuildingsForUser->contains('building_id', $building->id);
        }

        // they can always view a building.
        return (bool) $user->hasRoleAndIsCurrentRole(['coordinator', 'cooperation-admin']);
    }

    /**
     * Check if a building has allowed access to his building
     *
     * @param User $user
     * @param int $buildingId
     * @return bool
     */
    public function accessBuilding(User $user, int $buildingId): bool
    {
        $conversationRequest = PrivateMessage::forMyCooperation()->conversationRequestByBuildingId($buildingId)->first();

        if ($conversationRequest instanceof PrivateMessage && $conversationRequest->allow_access) {
            return true;
        }
        return false;
    }

    /**
     * Determine if a user can access his building
     *
     * @param User $user
     * @param int $buildingId
     * @return bool
     */
    public function userCanAccessBuilding(User $user, int $buildingId): bool
    {
        $buildingPermission = Building::withTrashed()->find($buildingId)->buildingPermissions()->where('user_id', $user->id)->first();
        if ($this->accessBuilding($user, $buildingId) && $buildingPermission instanceof BuildingPermission) {
            return true;
        }
        return false;
    }
}
