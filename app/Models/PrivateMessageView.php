<?php

namespace App\Models;

use App\Helpers\HoomdossierSession;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\PrivateMessageView
 *
 * @property int $id
 * @property int $private_message_id
 * @property int|null $user_id
 * @property int|null $cooperation_id
 * @property \Illuminate\Support\Carbon|null $read_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
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
    protected $fillable = [
        'private_message_id', 'user_id', 'cooperation_id', 'read_at'
    ];

    protected $casts = [
        'read_at' => 'datetime'
    ];

    /**
     * Get the total unread messages from a auth user
     *
     * @return int
     */
    public static function getTotalUnreadMessages()
    {
        // if the user is loggen in as a coordinator or cooperation admin
        if (\Auth::user()->hasRoleAndIsCurrentRole(['coordinator', 'cooperation-admin'])) {
            return self::where('cooperation_id', HoomdossierSession::getCooperation())
                ->where('read_at', null)
                ->count();
        } else {
            return self::where('user_id', \Auth::id())
                ->where('read_at', null)
                ->count();
        }
    }

    public static function isMessageUnread($privateMessage)
    {
        // if the user is loggen in as a coordinator or cooperation admin
        if (\Auth::user()->hasRoleAndIsCurrentRole(['coordinator', 'cooperation-admin'])) {
            $privateMessageView = PrivateMessageView::where('private_message_id', $privateMessage->id)
                ->where('cooperation_id', HoomdossierSession::getCooperation())->first();
            if ($privateMessageView instanceof PrivateMessageView && is_null($privateMessageView->read_at)) {
                return true;
            }
            return false;
        } else {
            $privateMessageView = PrivateMessageView::where('private_message_id', $privateMessage->id)->where('user_id', \Auth::id())->first();
            if ($privateMessageView instanceof PrivateMessageView && is_null($privateMessageView->read_at)) {
                return true;
            }
            return false;
        }
    }
}
