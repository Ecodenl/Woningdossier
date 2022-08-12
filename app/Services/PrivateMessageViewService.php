<?php

namespace App\Services;

use App\Events\PrivateMessageReceiverEvent;
use App\Models\Cooperation;
use App\Models\InputSource;
use App\Models\PrivateMessage;
use App\Models\PrivateMessageView;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class PrivateMessageViewService
{
    /**
     * Make a collection of private messages as read by a resident or coach
     * These have a user ID - input source ID combination.
     */
    public static function markAsReadByUser(Collection $privateMessages, User $user, InputSource $inputSource)
    {
        foreach ($privateMessages as $privateMessage) {
            PrivateMessageView::where('private_message_id', '=', $privateMessage->id)
                ->where('user_id', '=', $user->id)
                ->where('input_source_id', '=', $inputSource->id)
                ->update(['read_at' => Carbon::now()]);
        }
    }

    /**
     * Mark a collection of private messages as read by a cooperation
     * These have a cooperation ID.
     */
    public static function markAsReadByCooperation(Collection $privateMessages, Cooperation $cooperation)
    {
        foreach ($privateMessages as $privateMessage) {
            PrivateMessageView::where('private_message_id', '=', $privateMessage->id)
                ->where('to_cooperation_id', $cooperation->id)
                ->update(['read_at' => Carbon::now()]);
        }
    }
}
