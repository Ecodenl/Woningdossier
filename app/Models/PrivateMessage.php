<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrivateMessage extends Model
{
    protected $fillable = ['message', 'from_user_id', 'to_user_id', 'from_cooperation_id', 'to_cooperation_id', 'status', 'main_message', 'title'];

    const LINKED_TO_COACH = "gekoppeld aan coach";
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
     * Scope a query to return the messages that are sent to a user / coach
     *
     * @return PrivateMessage
     */
    public function scopeMyPrivateMessages($query)
    {
        return $query->where('to_user_id', \Auth::id());
    }

    /**
     * Scope a query to return the current open coach conversation request
     *
     * @return PrivateMessage
     */
    public function scopeMyCoachConversationRequest($query)
    {
        return $query
            ->where('from_user_id', \Auth::id())
            ->where('to_cooperation_id', \Session::get('cooperation'));
    }

    /**
     * Scope a query to return the full conversation between a coach and a user based on the main message
     *
     * @return $this
     */
    public static function getCoachConversation($mainMessageId)
    {

        $mainMessage = self::find($mainMessageId);

        $coachConversation = self::where('main_message', $mainMessageId)
            ->where('to_user_id', \Auth::id())
            ->orWhere('from_user_id', \Auth::id())
            ->where('from_cooperation_id', null)
            ->where('to_cooperation_id', null)
            ->get();

        $coachConversation->push($mainMessage);


        return $coachConversation;
    }

    public function scopeMainMessages($query)
    {
        return $query->where('is_completed', false)->where('main_message', null)->where('to_user_id', \Auth::id());
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
    public static function hasUserResponseToCoachConversationRequest()
    {
        if (self::myCoachConversationRequest()->first() != null && self::myCoachConversationRequest()->first()->status == self::LINKED_TO_COACH) {
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
}
