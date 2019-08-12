<?php

namespace App\Services;

use App\Events\PrivateMessageReceiverEvent;
use App\Helpers\Hoomdossier;
use App\Helpers\HoomdossierSession;
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
     * Create a private message view for each group participant.
     *
     * @param PrivateMessage $privateMessage
     */
    public static function create(PrivateMessage $privateMessage)
    {
        event(new PrivateMessageReceiverEvent($privateMessage));
    }


    /**
     * Make a collection of private messages as read by a resident or coach
     * These have a user ID - input source ID combination.
     *
     * @param  Collection  $privateMessages
     * @param  User  $user
     * @param  InputSource  $inputSource
     */
    public static function markAsReadByUser(Collection $privateMessages, User $user, InputSource $inputSource){
        $privateMessages->where('user_id', '=', $user->id)
            ->where('input_source_id', '=', $inputSource->id)
            ->update(['read_at' => Carbon::now()]);
    }

    /**
     * Mark a collection of private messages as read by a cooperation
     * These have a cooperation ID.
     *
     * @param  Collection  $privateMessages
     * @param  Cooperation  $cooperation
     */
    public static function markAsReadByCooperation(Collection $privateMessages, Cooperation $cooperation){
        $privateMessages->where('to_cooperation_id', $cooperation->id)
            ->update(['read_at' => Carbon::now()]);
    }



    /**
     * Sets the incoming messages to read.
     *
     * @param Collection $privateMessages
     * @param null|InputSource $inputSource
     * @param null|Cooperation $cooperation
     */
    public static function setRead(Collection $privateMessages, InputSource $inputSource = null, Cooperation $cooperation = null)
    {


        foreach ($privateMessages as $privateMessage) {
            $privateMessageQuery = PrivateMessageView::where('private_message_id', $privateMessage->id);

            if ($cooperation instanceof Cooperation){

            }
            elseif ($inputSource instanceof InputSource)
            if (\App\Helpers\Hoomdossier::user()->hasRoleAndIsCurrentRole(['coordinator', 'cooperation-admin'])) {
                $privateMessageQuery
                    ->where('to_cooperation_id', HoomdossierSession::getCooperation())
                    ->update(['read_at' => Carbon::now()]);
            } else {
                $privateMessageQuery
                    ->where('user_id', Hoomdossier::user()->id)
                    ->update(['read_at' => Carbon::now()]);
            }
        }
    }
}
