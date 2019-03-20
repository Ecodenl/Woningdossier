<?php

namespace App\Models;

use App\Helpers\HoomdossierSession;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * App\Models\PrivateMessage.
 *
 * @property int $id
 * @property int|null $building_id
 * @property bool|null $is_public
 * @property string $from_user
 * @property string|null $request_type
 * @property string $message
 * @property int|null $from_user_id
 * @property int|null $from_cooperation_id
 * @property int|null $to_cooperation_id
 * @property bool $allow_access
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \App\Models\Building|null $building
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PrivateMessage conversation($buildingId)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PrivateMessage conversationRequestByBuildingId($buildingId)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PrivateMessage forMyCooperation()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PrivateMessage myCoachConversationRequest()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PrivateMessage myOpenConversationRequest()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PrivateMessage myPrivateMessages()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PrivateMessage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PrivateMessage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PrivateMessage openCooperationConversationRequests()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PrivateMessage private()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PrivateMessage privateMessagesIdsGroupedByBuildingId()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PrivateMessage public()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PrivateMessage query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PrivateMessage unreadMessages()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PrivateMessage whereAllowAccess($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PrivateMessage whereBuildingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PrivateMessage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PrivateMessage whereFromCooperationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PrivateMessage whereFromUser($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PrivateMessage whereFromUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PrivateMessage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PrivateMessage whereIsPublic($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PrivateMessage whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PrivateMessage whereRequestType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PrivateMessage whereToCooperationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PrivateMessage whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class PrivateMessage extends Model
{
    const STATUS_LINKED_TO_COACH = 'gekoppeld aan coach';
    const STATUS_IN_CONSIDERATION = 'in behandeling';
    const STATUS_APPLICATION_SENT = 'aanvraag verzonden';

    const REQUEST_TYPE_USER_CREATED_BY_COOPERATION = 'user-created-by-cooperation';
    const REQUEST_TYPE_COACH_CONVERSATION = 'coach-conversation';
    const REQUEST_TYPE_MORE_INFORMATION = 'more-information';
    const REQUEST_TYPE_QUOTATION = 'quotation';
    const REQUEST_TYPE_OTHER = 'other';

    protected $fillable = [
        'message', 'from_user_id', 'from_cooperation_id', 'to_cooperation_id',
        'request_type', 'allow_access', 'building_id', 'from_user', 'is_public',
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

    public function scopeForMyCooperation($query)
    {
        return $query->where('to_cooperation_id', HoomdossierSession::getCooperation());
    }

    /**
     * Scope a conversation requests for a building.
     *
     * @param $query
     * @param $buildingId
     *
     * @return mixed
     */
    public function scopeConversationRequestByBuildingId($query, $buildingId)
    {
        return $query->public()->conversation($buildingId)->where('request_type', '!=', null);
    }

    /**
     * Scope a query to return all the conversation requests
     *
     * @param $query
     *
     * @return mixed
     */
    public function scopeConversationRequest($query)
    {
        return $query->public()->whereNotNull('request_type');
    }


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
     * Return the private message ids based on the Auth user has permission to.
     *
     * A "group" is defined by its building_id, one building_id has one group
     * however, a coach or coordinator can access multiple groups. This because they need to talk to multiple residents / buildings
     * in that case we return multiple private messages ids
     *
     * @param $query
     *
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

                    // no break
            case 'coach':

            case 'coordinator':
        }

        return $query;
    }

    /**
     * Determine if a private message is public.
     *
     * @param PrivateMessage $privateMessage
     *
     * @return bool
     */
    public static function isPublic(PrivateMessage $privateMessage)
    {
        if ($privateMessage->is_public) {
            return true;
        }

        return false;
    }

    /**
     * Determine if a private message is private.
     *
     * @param PrivateMessage $privateMessage
     *
     * @return bool
     */
    public static function isPrivate(PrivateMessage $privateMessage)
    {
        return ! self::isPublic($privateMessage);
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
     * Scope a query to return the conversation ordered on created_at.
     *
     * @return $this
     */
    public static function scopeConversation($query, $buildingId)
    {
        return $query->where('building_id', $buildingId)->orderBy('created_at');
    }

    /**
     * Scope the public messages.
     *
     * @param $query
     *
     * @return mixed
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    /**
     * Scope the private messages.
     *
     * @param $query
     *
     * @return mixed
     */
    public function scopePrivate($query)
    {
        return $query->where('is_public', false);
    }

    /**
     * Return the full name, just a wrap.
     *
     * @return mixed
     */
    public function getSender()
    {
        return $this->from_user;
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
     * returns a collection of all the participants for a chat from a building.
     *
     * @param $buildingId
     * @param bool $publicConversation
     *
     * @return Collection
     */
    public static function getGroupParticipants($buildingId, $publicConversation = true): Collection
    {
        // get the coaches with access to the building
        $coachesWithAccess = BuildingCoachStatus::getConnectedCoachesByBuildingId($buildingId);

        // create a collection of group members
        $groupMembers = collect();

        // if its a public conversation we push the building owner in it
        if ($publicConversation) {
            // get the owner of the building,
            $groupMembers->push(Building::withTrashed()->find($buildingId)->user);
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
    public function isMyMessage(): bool
    {
        // a coordinator and cooperation admin talks from a cooperation, not from his own name.
        if (\Auth::user()->hasRoleAndIsCurrentRole(['coordinator', 'cooperation-admin'])) {
            if ($this->from_cooperation_id == HoomdossierSession::getCooperation()) {
                return true;
            }
        // if a user would be a coach and a coordinator / cooperation-admin and he would be sending from the coordinator section.
        // after that switching back to the coach section and start to send message as a coach, he would be see the messages he sent as a coordinator as they were his messages
        // while this is true, its looks odd.
        } elseif (\Auth::id() == $this->from_user_id && is_null($this->from_cooperation_id)) {
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

    /**
     * Get the building from a message.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function building()
    {
        return $this->belongsTo(Building::class);
    }

    /**
     * Scope a query to returned the messages where building access is allowed.
     *
     * @param $query
     *
     * @return mixed
     */
    public function scopeAccessAllowed($query)
    {
        return $query->where('allow_access', true);
    }
}
