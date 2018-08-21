<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrivateMessage extends Model
{
    protected $fillable = ['message', 'from_user_id', 'to_user_id', 'from_cooperation_id', 'to_cooperation_id', 'status', 'main_message', 'title', 'request_type'];

    const STATUS_LINKED_TO_COACH = "gekoppeld aan coach";
    const STATUS_IN_CONSIDERATION = "in behandeling";

    const REQUEST_TYPE_COACH_CONVERSATION = "coach_conversation";
    const REQUEST_TYPE_MORE_INFORMATION = "more_information";
    const REQUEST_TYPE_QUOTATION = "quotation";

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'is_completed' => 'boolean',
        'from_user_read' => 'boolean',
        'to_user_read' => 'boolean',
    ];


    /**
     * Scope a query to return the open conversation requests based on the cooperation id
     *
     * @param $query
     * @return mixed
     */
    public function scopeOpenCooperationConversationRequests($query)
    {
        $currentCooperationId = session('cooperation');

        return $query->where('to_cooperation_id', $currentCooperationId)->where('status', self::STATUS_IN_CONSIDERATION);
    }

    /**
     * Scope a query to return the messages that are sent to a user / coach
     *
     * @return PrivateMessage
     */
    public function scopeMyPrivateMessages($query)
    {
        return $query->where('to_user_id', \Auth::id());
    }

    /**
     * Scope a query to return the current open conversation requests
     *
     * @return PrivateMessage
     */
    public function scopeMyConversationRequest($query)
    {
        return $query
            ->where('from_user_id', \Auth::id())
            ->where('to_cooperation_id', session('cooperation'));
    }

    /**
     * Scope a query to return the coach conversation request
     *
     * @param $query
     * @return mixed
     */
    public function scopeMyCoachConversationRequest($query)
    {
        return $query
            ->where('from_user_id', \Auth::id())
            ->where('to_cooperation_id', session('cooperation'))
            ->where('request_type', self::REQUEST_TYPE_COACH_CONVERSATION);
    }

    public function scopeMyOpenCoachConversationRequest($query)
    {
        return $query
            ->where('from_user_id', \Auth::id())
            ->where('to_cooperation_id', session('cooperation'))
            ->where('request_type', self::REQUEST_TYPE_COACH_CONVERSATION)
            ->where('status', self::STATUS_IN_CONSIDERATION);
    }


    /**
     * Scope a query to return the full conversation between a coach and a user based on the main message
     *
     * @return $this
     */
    public static function scopeConversation($query, $mainMessageId)
    {
        return $query->where('id', $mainMessageId)->orWhere('main_message', $mainMessageId);
    }

    /**
     * Get the main messages for a person who will recieves messages
     *
     * @param $query
     * @return mixed
     */
    public function scopeMainMessages($query)
    {
        return $query->where('is_completed', false)->where('main_message', null)->where('to_user_id', \Auth::id());
    }

    /**
     * Get the main messages for a person who sended / created the message
     *
     * @param $query
     * @return mixed
     */
    public function scopeMyCreatedMessages($query)
    {
        return $query->where('is_completed', false)->where('main_message', null)->where('from_user_id', \Auth::id());
    }

    /**
     * Return the sender information
     *
     * @param $messageId
     * @return mixed|static
     */
    public function getSender($messageId)
    {
        $senderId = $this->find($messageId)->from_user_id;

        $sender = User::find($senderId);

        return $sender;
    }

    /**
     * Return info about the receiver of the message
     *
     * @param $messageId
     * @return mixed|static
     */
    public function getReceiver($messageId)
    {
        $receiverId = $this->find($messageId)->to_user_id;

        $receiver = User::find($receiverId);

        return $receiver;
    }

    /**
     * Check if its the user his message
     *
     * @return bool
     */
    public function isMyMessage()
    {
        if ($this->from_user_id == \Auth::id()) {
            return true;
        }

        return false;
    }

    /**
     * Check if the user has response to his conversation request
     *
     * @return bool
     */
    public static function hasUserResponseToConversationRequest()
    {
        if (self::myConversationRequest()->first() != null && self::myConversationRequest()->first()->status == self::STATUS_LINKED_TO_COACH) {
            return true;
        }

        return false;
    }

    /**
     * Check if the user has response to his coach conversation request
     *
     * @return bool
     */
    public static function hasUserResponseToCoachConversationRequest()
    {
        if (self::myCoachConversationRequest()->first() != null && self::myCoachConversationRequest()->first()->status == self::STATUS_LINKED_TO_COACH) {
            return true;
        }

        return false;
    }

    /**
     * Scope a query to get the unread messages from a user
     *
     * @param $query
     * @return mixed
     */
    public function scopeUnreadMessages($query)
    {
        return $query->where('to_user_id', \Auth::id())->where('to_user_read', false);
    }


    /**
     * Check if the user has unread messages based on the main message
     *
     * if you want to check if a specific message has been read use the isRead() function.
     *
     * @return bool
     */
    public function hasUserUnreadMessages()
    {
        $answers = $this->where('main_message', $this->id)->where('to_user_id', \Auth::id())->get();

        return $answers->contains('to_user_read', false);
    }

    /**
     * Check if a user has read his message
     *
     * @return bool
     */
    public function isRead()
    {
        if ($this->to_user_id == \Auth::id() && $this->to_user_read == true) {
            return true;
        }

        return false;
    }

    /**
     * Check wheter a conversation request has been read, this can only be used on conversation requests
     * Cause we search on cooperation id and not on user id
     *
     * @return bool
     */
    public function isConversationRequestRead()
    {
        if ($this->to_cooperation_id == session('cooperation') && $this->to_user_read == true) {
            return true;
        }

        return false;
    }

    /**
     * Check if the request is a coach conversation request
     *
     * @return bool
     */
    public function isCoachRequestConversation()
    {
        if ($this->request_type == self::REQUEST_TYPE_COACH_CONVERSATION) {
            return true;
        }

        return false;
    }


}
