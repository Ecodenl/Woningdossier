<?php

namespace App\Models;

use App\Helpers\Hoomdossier;
use App\Helpers\HoomdossierSession;
use App\Traits\HasCooperationTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

/**
 * App\Models\PrivateMessage
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
 * @property-read \App\Models\Building|null $building
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\PrivateMessageView[] $privateMessageViews
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PrivateMessage accessAllowed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PrivateMessage conversation($buildingId)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PrivateMessage conversationRequest()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PrivateMessage conversationRequestByBuildingId($buildingId)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PrivateMessage forMyCooperation()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PrivateMessage myPrivateMessages()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PrivateMessage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PrivateMessage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PrivateMessage private()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PrivateMessage public()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PrivateMessage query()
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
    use HasCooperationTrait;

    const STATUS_LINKED_TO_COACH = 'gekoppeld aan coach';
    const STATUS_IN_CONSIDERATION = 'in behandeling';
    const STATUS_APPLICATION_SENT = 'aanvraag verzonden';

    const REQUEST_TYPE_USER_CREATED_BY_COOPERATION = 'user-created-by-cooperation';
    const REQUEST_TYPE_COACH_CONVERSATION = 'coach-conversation';
    const REQUEST_TYPE_MORE_INFORMATION = 'more-information';
    const REQUEST_TYPE_QUOTATION = 'quotation';
    const REQUEST_TYPE_OTHER = 'other';

    protected $fillable = [
        'message', 'from_user_id', 'cooperation_id', 'from_cooperation_id', 'to_cooperation_id',
        'request_type', 'allow_access', 'building_id', 'from_user', 'is_public',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'allow_access' => 'boolean',
        'is_public'    => 'boolean',
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

    /**
     * Determine if a private message is public.
     *
     * @param  PrivateMessage  $privateMessage
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
     * @param  PrivateMessage  $privateMessage
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
     * @param        $buildingId
     * @param  bool  $publicConversation
     *
     * @return Collection
     */
    public static function getGroupParticipants($buildingId, $publicConversation = true): Collection
    {
	    // create a collection of group members
	    $groupMembers = collect();

	    $building = Building::find($buildingId);

	    if ($building instanceof Building) {

		    // get the coaches with access to the building
		    $coachesWithAccess = BuildingCoachStatus::getConnectedCoachesByBuildingId( $buildingId );

		    // if its a public conversation we push the building owner in it
		    if ( $publicConversation ) {
			    // get the owner of the building,
			    if ($building->user instanceof User) {
				    $groupMembers->push( $building->user );
			    }
		    }

		    // put the coaches with access to the groupmembers
		    foreach ( $coachesWithAccess as $coachWithAccess ) {
			    $groupMembers->push( User::find( $coachWithAccess->coach_id ) );
		    }
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
        if (\App\Helpers\Hoomdossier::user()->hasRoleAndIsCurrentRole(['coordinator', 'cooperation-admin'])) {
            if ($this->from_cooperation_id == HoomdossierSession::getCooperation()) {
                return true;
            }
            // if a user would be a coach and a coordinator / cooperation-admin and he would be sending from the coordinator section.
            // after that switching back to the coach section and start to send message as a coach, he would be see the messages he sent as a coordinator as they were his messages
            // while this is true, its looks odd.
        } else if (Hoomdossier::user()->id == $this->from_user_id && is_null($this->from_cooperation_id)) {
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

    /**
     * Check if its allowed to access a building by its given building id.
     *
     * @param $buildingId
     *
     * @return bool
     */
    public static function allowedAccess($buildingId)
    {
        return static::forMyCooperation()->conversationRequestByBuildingId($buildingId)->accessAllowed()->first() instanceof PrivateMessage;
    }

    /**
     * Get the private message views
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function privateMessageViews(): HasMany
    {
        return $this->hasMany(PrivateMessageView::class);
    }
}
