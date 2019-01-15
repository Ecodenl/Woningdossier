<?php

namespace App\Models;

use App\Helpers\HoomdossierSession;
use Illuminate\Database\Eloquent\Model;

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
        if (\Auth::user()->hasRole(['coordinator', 'cooperation-admin']) && in_array(Role::find(HoomdossierSession::getRole())->name, ['coordinator', 'cooperation-admin'])) {
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
        if (\Auth::user()->hasRole(['coordinator', 'cooperation-admin']) && in_array(Role::find(HoomdossierSession::getRole())->name, ['coordinator', 'cooperation-admin'])) {
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
