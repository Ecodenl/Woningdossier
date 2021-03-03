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
            return static::getTotalUnreadMessagesForCooperation(HoomdossierSession::getCooperation());
        } else {
            return static::getTotalUnreadMessagesForUserWithInputSource(Hoomdossier::user()->id, HoomdossierSession::getInputSource());
        }
    }

    /**
     * Get the number messages that have been sent to the cooperation.
     *
     * @param int $cooperationId
     */
    public static function getTotalUnreadMessagesForCooperation($cooperationId): int
    {
        return PrivateMessageView::where('to_cooperation_id', $cooperationId)
            ->whereNull('input_source_id')
            ->where('read_at', null)
            ->count();
    }

    public static function getTotalUnreadMessagesForUserWithInputSource($userId, $inputSourceId): int
    {
        return PrivateMessageView::select('private_messages.*')
            ->where('private_message_views.user_id', '=', $userId)
            ->where('input_source_id', '=', $inputSourceId)
            ->where('read_at', null)
            ->join('private_messages', function ($query) {
                $query->on('private_message_views.private_message_id', '=', 'private_messages.id');
            })->count();
    }

    /**
     * Get the total unread messages for a user within its given cooperation and after a specific date.
     *
     * @param  $specificDate
     */
    public static function getTotalUnreadMessagesForUserAndCooperationAfterSpecificDate(User $user, Cooperation $cooperation, $specificDate): int
    {
        $cooperationUnreadMessagesCount = 0;

        // if the user has the role coordinator or cooperation-admin get them as well
        if ($user->hasRole(['coordinator', 'cooperation-admin'])) {
            $cooperationUnreadMessagesCount = PrivateMessageView::where('to_cooperation_id', $cooperation->id)
                ->where('created_at', '>=', $specificDate)
                ->where('read_at', null)
                ->count();
        }

        // get the unread messages for the user itself within its given cooperation after a given date.
        $userUnreadMessages = PrivateMessageView::select('private_messages.*')
            ->where('private_message_views.user_id', $user->id)
            ->where('read_at', null)
            ->where('private_message_views.created_at', '>=', $specificDate)
            ->join('private_messages', function ($query) {
                $query->on('private_message_views.private_message_id', '=', 'private_messages.id');
            })->count();

        $totalUnreadMessagesCount = $userUnreadMessages + $cooperationUnreadMessagesCount;

        return $totalUnreadMessagesCount;
    }

    /**
     * Get the total unread messages for a user, this also counts the unread messages from the admin side.
     *
     * @return int
     */
    public static function getTotalUnreadMessagesForUser(User $user, Cooperation $cooperation)
    {
        $cooperationUnreadMessagesCount = 0;

        // if the user has the role coordinator or cooperation-admin get them as well
        if ($user->hasRole(['coordinator', 'cooperation-admin'])) {
            $cooperationUnreadMessagesCount = PrivateMessageView::where('to_cooperation_id', $cooperation->id)
                ->where('read_at', null)
                ->count();
        }

        // get the unread messages for the user itsel.
        $userUnreadMessages = PrivateMessageView::where('user_id', $user->id)
            ->forCurrentInputSource()
            ->where('read_at', null)
            ->count();

        $totalUnreadMessagesCount = $userUnreadMessages + $cooperationUnreadMessagesCount;

        return $totalUnreadMessagesCount;
    }

    /**
     * Get the unread messages count for a given building. The count will be determined on the auth user his role and user id.
     */
    public static function getTotalUnreadMessagesCountByBuildingForAuthUser(Building $building): int
    {
        // get all the private message id's for a building
        $privateMessageIdsForBuilding = $building->privateMessages
            ->pluck('id')
            ->all();

        // get the unread messages for the cooperation
        if (\App\Helpers\Hoomdossier::user()->hasRoleAndIsCurrentRole(['coordinator', 'cooperation-admin'])) {
            return PrivateMessageView::where('to_cooperation_id', HoomdossierSession::getCooperation())
                ->whereIn('private_message_id', $privateMessageIdsForBuilding)
                ->whereNull('read_at')
                ->count();
        } else {
            return PrivateMessageView::where('user_id', Hoomdossier::user()->id)
                ->forCurrentInputSource()
                ->whereIn('private_message_id', $privateMessageIdsForBuilding)
                ->whereNull('read_at')
                ->count();
        }
    }
}