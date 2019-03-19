<?php

namespace App\Policies;

use App\Models\Building;
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
     * Check if a building has allowed access to his building
     *
     * @param User $user
     * @param int $buildingId
     * @return bool
     */
    public function accessBuilding(User $user, int $buildingId): bool
    {
        $conversationRequest = PrivateMessage::conversationRequest($buildingId)->first();

        if ($conversationRequest instanceof PrivateMessage && $conversationRequest->allow_access) {
            return true;
        }
        return false;
    }
}
