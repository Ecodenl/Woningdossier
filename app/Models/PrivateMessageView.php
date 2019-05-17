<?php

namespace App\Models;

use App\Helpers\HoomdossierSession;
use App\Traits\GetMyValuesTrait;
use App\Traits\GetValueTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\PrivateMessageView.
 *
 * @property int                             $id
 * @property int                             $private_message_id
 * @property int|null                        $user_id
 * @property int|null                        $cooperation_id
 * @property \Illuminate\Support\Carbon|null $read_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PrivateMessageView newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PrivateMessageView newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PrivateMessageView query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PrivateMessageView whereCooperationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PrivateMessageView whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PrivateMessageView whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PrivateMessageView wherePrivateMessageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PrivateMessageView whereReadAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PrivateMessageView whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PrivateMessageView whereUserId($value)
 * @mixin \Eloquent
 */
class PrivateMessageView extends Model
{

    use GetMyValuesTrait;

    protected $fillable = [
        'input_source_id', 'private_message_id', 'user_id', 'cooperation_id', 'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    /**
     * Query to scope records for the current input source.
     *
     * Normally we would use the GetValueTrait which applies the global GetValueScope.
     *
     * BUT: the input_source_id will sometimes be empty (coordinator and cooperation-admin), so we cant use the global scope.
     *
     * @param $query
     *
     * @return mixed
     */
    public function scopeForCurrentInputSource($query)
    {
        return $query->where('input_source_id', HoomdossierSession::getInputSourceValue());
    }

    /**
     * Get the total unread messages for a user, this also counts the unread messages from the admin side.
     *
     * @param  User         $user
     * @param  Cooperation  $cooperation
     *
     * @return int
     */
    public static function getTotalUnreadMessagesForUser(User $user, Cooperation $cooperation)
    {
        $cooperationUnreadMessagesCount = 0;

        // if the user has the role coordinator or cooperation-admin get them as well
        if ($user->hasRole(['coordinator', 'cooperation-admin'])) {
            $cooperationUnreadMessagesCount = static::where('cooperation_id', $cooperation->id)
                                                    ->where('read_at', null)
                                                    ->count();
        }

        // get the unread messages for the user itsel.
        $userUnreadMessages = static::where('user_id', $user->id)
                                    ->forCurrentInputSource()
                                    ->where('read_at', null)
                                    ->count();

        $totalUnreadMessagesCount = $userUnreadMessages + $cooperationUnreadMessagesCount;

        return $totalUnreadMessagesCount;
    }

    /**
     * Get the total unread messages from a auth user.
     *
     * @return int
     */
    public static function getTotalUnreadMessagesForCurrentRole()
    {
        // if the user is loggen in as a coordinator or cooperation admin
        if (\Auth::user()->hasRoleAndIsCurrentRole(['coordinator', 'cooperation-admin'])) {
            return static::where('cooperation_id', HoomdossierSession::getCooperation())
                         ->where('read_at', null)
                         ->count();
        } else {
            return static::where('user_id', \Auth::id())
                         ->where('read_at', null)
                         ->forCurrentInputSource()
                         ->count();
        }
    }


    /**
     * Get the unread messages count for a given building. The count will be determined on the auth user his role and user id.
     *
     * @param  Building  $building
     *
     * @return int
     */
    public static function getTotalUnreadMessagesCountByBuildingForAuthUser(Building $building): int
    {

        // get all the private message id's for a building
        $privateMessageIdsForBuilding = $building->privateMessages()
                                                 ->select('id')
                                                 ->get()
                                                 ->pluck('id')
                                                 ->all();

        // get the unread messages for the cooperation
        if (\Auth::user()->hasRoleAndIsCurrentRole(['coordinator', 'cooperation-admin'])) {
            return static::where('cooperation_id', HoomdossierSession::getCooperation())
                         ->whereIn('private_message_id', $privateMessageIdsForBuilding)
                         ->whereNull('read_at')
                         ->count();
        } else {
            return static::where('user_id', \Auth::id())
                         ->forCurrentInputSource()
                         ->whereIn('private_message_id', $privateMessageIdsForBuilding)
                         ->whereNull('read_at')
                         ->count();
        }
    }

    /**
     * Check if a private message is left unread
     *
     * @param $privateMessage
     *
     * @return bool
     */
    public static function isMessageUnread($privateMessage): bool
    {
        // if the user is logged in as a coordinator or cooperation admin
        if (\Auth::user()->hasRoleAndIsCurrentRole(['coordinator', 'cooperation-admin'])) {
            $privateMessageView = static::where('private_message_id', $privateMessage->id)
                                        ->where('cooperation_id', HoomdossierSession::getCooperation())->first();
            if ($privateMessageView instanceof PrivateMessageView && is_null($privateMessageView->read_at)) {
                return true;
            }

            return false;
        } else {
            $privateMessageView = static::where('private_message_id', $privateMessage->id)
                                        ->forCurrentInputSource()
                                        ->where('user_id', \Auth::id())
                                        ->first();

            if ($privateMessageView instanceof PrivateMessageView && is_null($privateMessageView->read_at)) {
                return true;
            }

            return false;
        }
    }
}
