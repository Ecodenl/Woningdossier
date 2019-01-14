<?php

namespace App\Models;

use App\Helpers\HoomdossierSession;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class PrivateMessage extends Model
{
    const STATUS_LINKED_TO_COACH = 'gekoppeld aan coach';
    const STATUS_IN_CONSIDERATION = 'in behandeling';
    const STATUS_APPLICATION_SENT = 'aanvraag verzonden';

    const REQUEST_TYPE_COACH_CONVERSATION = 'coach-conversation';
    const REQUEST_TYPE_MORE_INFORMATION = 'more-information';
    const REQUEST_TYPE_QUOTATION = 'quotation';
    const REQUEST_TYPE_OTHER = 'other';

    protected $fillable = [
        'message', 'from_user_id', 'to_user_id', 'from_cooperation_id', 'to_cooperation_id',
        'request_type', 'allow_access', 'building_id', 'from_user'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'allow_access' => 'boolean',
        'is_public' => 'boolean',
    ];

    public static function isConversationRequestConnectedToCoach($conversationRequest)
    {
        return self::STATUS_LINKED_TO_COACH == $conversationRequest->status;
    }

    /**
     * Scope a query to return the open conversation requests based on the cooperation id.
     *
     * @param $query
     *
     * @return mixed
     */
    public function scopeOpenCooperationConversationRequests($query)
    {
        $currentCooperationId = HoomdossierSession::getCooperation();

        return $query->where('to_cooperation_id', $currentCooperationId);
//            ->where('status', self::STATUS_APPLICATION_SENT)->orWhere('status', self::STATUS_IN_CONSIDERATION);
    }

    /**
     * Return the private message ids based on the Auth user has permission to
     *
     * A "group" is defined by its building_id, one building_id has one group
     * however, a coach or coordinator can access multiple groups. This because they need to talk to multiple residents / buildings
     * in that case we return multiple private messages ids
     *
     * @param $query
     * @return mixed
     */
    public function scopePrivateMessagesIdsGroupedByBuildingId($query)
    {
        $role = Role::find(HoomdossierSession::getRole());
        // we call it short
        $roleShort = $role->name;

        switch ($roleShort) {
            case 'resident':
                $query = $query
                    ->where('building_id', HoomdossierSession::getBuilding())
                    ->orderBy('created_at');

            case 'coach':

            case 'coordinator':

        }

        return $query;
    }

    public function scopeMessageGroups($query)
    {
        $privateMessageIds = $this->privateMessagesIdsGroupedByBuildingId()->get();
        $role = Role::find(HoomdossierSession::getRole());
        // we call it short
        $roleShort = $role->name;

        switch ($roleShort) {
            case 'resident':
                $query
                    ->where('building_id', HoomdossierSession::getBuilding())
                    ->select('building_id')
                    ->groupBy('building_id')->get();

        }

        return $query;
    }
    
    /**
     * Scope a query to return the messages that are sent to a user / coach.
     *
     * @return PrivateMessage
     */
    public function scopeMyPrivateMessages($query)
    {
        return $query->where('to_user_id', \Auth::id());
    }

    /**
     * Scope a query to return the current open conversation requests.
     *
     * @return PrivateMessage
     */
    public function scopeMyConversationRequest($query)
    {
        return $query
            ->where('request_type', '!=', null)
            ->where('building_id', HoomdossierSession::getBuilding())
            ->where('to_cooperation_id', HoomdossierSession::getCooperation());
    }

    /**
     * Scope a query to return the coach conversation request.
     *
     * @param $query
     *
     * @return mixed
     */
    public function scopeMyCoachConversationRequest($query)
    {
        return $query
            ->where('from_user_id', \Auth::id())
            ->where('to_cooperation_id', session('cooperation'))
            ->where('request_type', self::REQUEST_TYPE_COACH_CONVERSATION);
    }

    public function scopeMyOpenConversationRequest($query)
    {
        return $query
            ->where('building_id', HoomdossierSession::getBuilding())
            ->where('to_cooperation_id', HoomdossierSession::getCooperation());
    }

    /**
     * Scope a query to return the conversation ordered on created_at
     *
     * @return $this
     */
    public static function scopeConversation($query, $buildingId)
    {
        return $query->where('building_id', $buildingId)->orderBy('created_at');
    }

    /**
     * Get the main messages for a person who will receives messages.
     *
     * @param $query
     *
     * @return mixed
     */
    public function scopeMainMessages($query)
    {
        return $query->where('is_completed', false)->where('main_message', null)->where('to_user_id', \Auth::id());
    }

    /**
     * Get the main messages for a person who sended / created the message.
     *
     * @param $query
     *
     * @return mixed
     */
    public function scopeMyCreatedMessages($query)
    {
        return $query->where('is_completed', false)->where('main_message', null)->where('from_user_id', \Auth::id());
    }

    /**
     * Return the sender information.
     *
     * @param int $messageId
     *
     * @return User|null
     */
//    public function getSender($messageId)
//    {
//        $senderId = $this->find($messageId)->from_user_id;
//        if (empty($senderId)){
//        	return null;
//        }
//
//        $sender = User::find($senderId);
//
//        return $sender;
//    }

    public function getSender()
    {
        return $this->from_user;
    }

    /**
     * Return info about the receiver of the message.
     *
     * @param int $messageId
     *
     * @return User|null
     */
    public function getReceiver($messageId)
    {
        $receiverId = $this->find($messageId)->to_user_id;
        if (empty($receiverId)) {
            return null;
        }

        $receiver = User::find($receiverId);

        return $receiver;
    }

    /**
     * Returns the receiving cooperation of this private message.
     *
     * @return Cooperation|null
     */
    public function getReceivingCooperation()
    {
        $receivingCooperationId = $this->to_cooperation_id;
        if (empty($receivingCooperationId)) {
            return null;
        }

        return Cooperation::find($receivingCooperationId);
    }

    /**
     * Returns the receiving cooperation of this private message.
     *
     * @return Cooperation|null
     */
    public function getSendingCooperation()
    {
        $sendingCooperationId = $this->from_cooperation_id;
        if (empty($sendingCooperationId)) {
            return null;
        }

        return Cooperation::find($sendingCooperationId);
    }


    /**
     * Get all the "group members"
     * returns a collection of all the participants for a chat from a building
     *
     * @param $buildingId
     * @param bool $publicConversation
     *
     * @return Collection
     */
    public static function getGroupParticipants($buildingId, $publicConversation = true): Collection
    {
        // all the buildingCoachStatuses for a building
        $buildingCoachStatuses = BuildingCoachStatus::where('building_id', $buildingId)->get();

        // filter the coaches that have access to a building
        $coachesWithAccess = $buildingCoachStatuses->filter(function ($buildingCoachStatus) {
            return BuildingCoachStatus::hasCoachAccess($buildingCoachStatus->building_id, $buildingCoachStatus->coach_id);
        })->unique('coach_id');

        // create a collection of group members
        $groupMembers = collect();

        // if its a public conversation we push the building owner in it
        if ($publicConversation) {
            // get the owner of the building,
            $groupMembers->push(Building::find($buildingId)->user);
        }

        // put the coaches with access to the groupmembers
        foreach ($coachesWithAccess as $coachWithAccess) {
            $groupMembers->push(User::find($coachWithAccess->coach_id));
        }


        return $groupMembers;
    }

    /**
     * Check if its the user his message.
     *
     * @return bool
     */
    public function isMyMessage()
    {
        // since we dont save a user id in any sort of form, we need to check the full name.
        $userFullName = \Auth::user()->first_name ." ". \Auth::user()->last_name;

        if ($this->from_user == $userFullName) {
            return true;
        }
        return false;
    }

    /**
     * Returns the opposite from isMyMessage().
     *
     * @return bool
     */
    public function isNotMyMessage(): bool
    {
        return ! $this->isMyMessage();
    }

    /**
     * Check if the user has response to his conversation request.
     *
     * @return bool
     */
    public static function hasUserResponseToConversationRequest()
    {
        if (null != self::myConversationRequest()->first() && self::STATUS_LINKED_TO_COACH == self::myConversationRequest()->first()->status) {
            return true;
        }

        return false;
    }

    /**
     * Check if the user has response to his coach conversation request.
     *
     * @return bool
     */
    public static function hasUserResponseToCoachConversationRequest()
    {
        if (null != self::myCoachConversationRequest()->first() && self::STATUS_LINKED_TO_COACH == self::myCoachConversationRequest()->first()->status) {
            return true;
        }

        return false;
    }

    /**
     * Scope a query to get the unread messages from a user.
     *
     * @param $query
     *
     * @return mixed
     */
    public function scopeUnreadMessages($query)
    {
        return $query;
    }

    /**
     * Check if a message is the main message.
     *
     * @return bool
     */
    public function isMainMessage(): bool
    {
        if (empty($this->main_message)) {
            return true;
        }

        return false;
    }

    /**
     * Check if the main message is read.
     *
     * @return bool
     */
    public function isMainMessageRead(): bool
    {
        // if its set to 1 it wil return true;
        // if the to user read is set to 0 it will return false
        return $this->to_user_read;
    }

    /**
     * Returns the opposite of isMainMessageRead();.
     *
     * @return bool
     */
    public function isMainMessageUnread(): bool
    {
        return ! $this->isMainMessageRead();
    }

    /**
     * Check if the user has unread messages based on the main message.
     *
     * @return bool
     */
    public function hasUserUnreadMessages(): bool
    {

        // new logic should be aplied here
        return true;
//        $answers = $this->where('main_message', $this->id)->where('to_user_id', \Auth::id())->get();
//
//        // $asnwers will be empty when there is no response to the main message
//        if ($answers->isNotEmpty()) {
//            return $answers->contains('to_user_read', false);
//        } elseif ($this->isMainMessage()) {
//            // we check if the main message is unread and if its not our message, you have always read your own message.
//            // unless your blind.
//            return $this->isMainMessageUnread() && $this->isNotMyMessage();
//        } else {
//            \Log::debug(__FUNCTION__.'Came to the else for message id: '.$this->id);
//        }
    }

    /**
     * Check wheter a conversation request has been read, this can only be used on conversation requests
     * Cause we search on cooperation id and not on user id.
     *
     * @return bool
     */
    public function isConversationRequestRead()
    {
        if ($this->to_cooperation_id == session('cooperation') && true == $this->to_user_read) {
            return true;
        }

        return false;
    }

    /**
     * Check if the message is a conversation request.
     *
     * @return bool
     */
    public function isConversationRequest(): bool
    {
        if (! empty($this->request_type)) {
            return true;
        }

        return false;
    }

    /**
     * Check if the request is a coach conversation request.
     *
     * @return bool
     */
    public function isCoachRequestConversation()
    {
        if (self::REQUEST_TYPE_COACH_CONVERSATION == $this->request_type) {
            return true;
        }

        return false;
    }
}
