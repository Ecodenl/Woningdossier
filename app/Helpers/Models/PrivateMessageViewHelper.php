<?php

namespace App\Helpers\Models;

use App\Helpers\Hoomdossier;
use App\Helpers\HoomdossierSession;
use App\Models\Building;
use App\Models\Cooperation;
use App\Models\PrivateMessageView;
use App\Models\User;

class PrivateMessageViewHelper
{
    /**
     * Get the total unread messages from a auth user.
     */
    public static function getTotalUnreadMessagesForCurrentRole(): int
    {
        // if the user his current role is coordinator or cooperation admin
        // then he talks as a cooperation itself, so we need to get the unread messages for the cooperation itself.
        if (Hoomdossier::user()->hasRoleAndIsCurrentRole(['coordinator', 'cooperation-admin'])) {
            return static::cooperationBaseQuery(HoomdossierSession::getCooperation())
                ->count();
        } else {
            return static::userBaseQuery(Hoomdossier::user()->id)
                ->where('input_source_id', '=', HoomdossierSession::getInputSource())
                ->count();
        }
    }

    /**
     * Get the total unread messages for a user within its given cooperation and after a specific date.
     *
     * @param User $user
     * @param $specificDate
     */
    public static function getTotalUnreadMessagesForUserAndCooperationAfterSpecificDate(User $user, $specificDate): int
    {
        $cooperationUnreadMessagesCount = 0;
        $cooperation = $user->cooperation;

        // if the user has the role coordinator or cooperation-admin get them as well
        if ($user->hasRole(['coordinator', 'cooperation-admin'])) {
            $cooperationUnreadMessagesCount = static::cooperationBaseQuery($cooperation->id)
                ->where('created_at', '>=', $specificDate)
                ->count();
        }

        // get the unread messages for the user itself within its given cooperation after a given date.
        $userUnreadMessages = static::userBaseQuery($user->id)
            ->where('created_at', '>=', $specificDate)
            ->count();

        return $userUnreadMessages + $cooperationUnreadMessagesCount;
    }

    /**
     * Get the unread messages count for a given building. The count will be determined on the auth user his role and user id.
     *
     * @param Building $building
     * @return int
     */
    public static function getTotalUnreadMessagesCountByBuilding(Building $building): int
    {
        // get all the private message id's for a building
        $privateMessageIdsForBuilding = $building->privateMessages
            ->pluck('id')
            ->all();

        // get the unread messages for the cooperation
        if (Hoomdossier::user()->hasRoleAndIsCurrentRole(['coordinator', 'cooperation-admin'])) {
            return static::cooperationBaseQuery(HoomdossierSession::getCooperation())
                ->whereIn('private_message_id', $privateMessageIdsForBuilding)
                ->count();
        } else {
            return static::userBaseQuery(Hoomdossier::user()->id)
                ->forCurrentInputSource()
                ->whereIn('private_message_id', $privateMessageIdsForBuilding)
                ->count();
        }
    }

    protected static function cooperationBaseQuery($cooperationId)
    {
        return PrivateMessageView::where('to_cooperation_id', $cooperationId)
            ->whereNull('input_source_id')
            ->whereNull('read_at');
    }

    protected static function userBaseQuery($userId)
    {
        return PrivateMessageView::where('user_id', $userId)
            ->whereNull('read_at');
    }
}