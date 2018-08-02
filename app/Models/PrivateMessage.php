<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrivateMessage extends Model
{
    protected $fillable = ['message', 'from_user_id', 'to_user_id', 'from_cooperation_id', 'to_cooperation_id', 'status'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'is_completed' => 'boolean',
    ];


    /**
     * Scope a query to return the messages that are sent to a user / coach
     *
     * @return PrivateMessage
     */
    public function scopeMyPrivateMessages()
    {
        return $this->where('to_user_id', \Auth::id());
    }

    /**
     * Scope a query to return the current open coach conversation request
     *
     * @return PrivateMessage
     */
    public function scopeMyCoachConversationRequest()
    {
        return $this
            ->where('from_user_id', \Auth::id())
            ->where('to_cooperation_id', \Session::get('cooperation'));
    }

    /**
     * Scope a query to return the full conversation between a coach and a user
     *
     * @return $this
     */
    public function scopeCoachConversation()
    {
        return $this->where('is_completed', false)
            ->where('to_user_id', \Auth::id())
            ->orWhere('from_user_id', \Auth::id())
            ->where('from_cooperation_id', null)
            ->where('to_cooperation_id', null);
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
}
