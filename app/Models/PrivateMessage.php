<?php

namespace App\Models;

use App\Helpers\Hoomdossier;
use App\Helpers\HoomdossierSession;
use App\Services\BuildingCoachStatusService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

/**
 * App\Models\PrivateMessage.
 *
 * @property int                                                                       $id
 * @property int|null                                                                  $building_id
 * @property bool|null                                                                 $is_public
 * @property string                                                                    $from_user
 * @property string                                                                    $message
 * @property int|null                                                                  $from_user_id
 * @property int|null                                                                  $from_cooperation_id
 * @property int|null                                                                  $to_cooperation_id
 * @property \Illuminate\Support\Carbon|null                                           $created_at
 * @property \Illuminate\Support\Carbon|null                                           $updated_at
 * @property \App\Models\Building|null                                                 $building
 * @property \Illuminate\Database\Eloquent\Collection|\App\Models\PrivateMessageView[] $privateMessageViews
 * @property int|null                                                                  $private_message_views_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder|PrivateMessage conversation($buildingId)
 * @method static \Illuminate\Database\Eloquent\Builder|PrivateMessage forMyCooperation()
 * @method static \Illuminate\Database\Eloquent\Builder|PrivateMessage myPrivateMessages()
 * @method static \Illuminate\Database\Eloquent\Builder|PrivateMessage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PrivateMessage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PrivateMessage private()
 * @method static \Illuminate\Database\Eloquent\Builder|PrivateMessage public()
 * @method static \Illuminate\Database\Eloquent\Builder|PrivateMessage query()
 * @method static \Illuminate\Database\Eloquent\Builder|PrivateMessage whereBuildingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PrivateMessage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PrivateMessage whereFromCooperationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PrivateMessage whereFromUser($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PrivateMessage whereFromUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PrivateMessage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PrivateMessage whereIsPublic($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PrivateMessage whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PrivateMessage whereToCooperationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PrivateMessage whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class PrivateMessage extends Model
{
    protected $fillable = [
        'message', 'from_user_id', 'cooperation_id', 'from_cooperation_id', 'to_cooperation_id',
        'building_id', 'from_user', 'is_public',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'is_public'    => 'boolean',
    ];

    public function scopeForMyCooperation($query)
    {
        return $query->where('to_cooperation_id', HoomdossierSession::getCooperation());
    }

    /**
     * Determine if a private message is public.
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
        return $query->where('to_user_id', Hoomdossier::user()->id);
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
     * @param      $buildingId
     * @param bool $publicConversation
     */
    public static function getGroupParticipants($buildingId, $publicConversation = true): Collection
    {
        // create a collection of group members
        $groupMembers = collect();

        $building = Building::find($buildingId);

        if ($building instanceof Building) {
            // get the coaches with access to the building
            $coachesWithAccess = BuildingCoachStatusService::getConnectedCoachesByBuildingId($buildingId);

            // if its a public conversation we push the building owner in it
            if ($publicConversation) {
                // get the owner of the building,
                if ($building->user instanceof User) {
                    $groupMembers->push($building->user);
                }
            }

            // put the coaches with access to the groupmembers
            foreach ($coachesWithAccess as $coachWithAccess) {
                $groupMembers->push(User::find($coachWithAccess->coach_id));
            }
        }

        return $groupMembers;
    }

    /**
     * Check if its the user his message.
     */
    public function isMyMessage(): bool
    {
        $user = Hoomdossier::user();

        // a coordinator and cooperation admin talks from a cooperation, not from his own name.
        if ($user->hasRoleAndIsCurrentRole(['coordinator', 'cooperation-admin'])) {
            if ($this->from_cooperation_id == HoomdossierSession::getCooperation()) {
                return true;
            }
            // if a user would be a coach and a coordinator / cooperation-admin and he would be sending from the coordinator section.
            // after that switching back to the coach section and start to send message as a coach, he would be see the messages he sent as a coordinator as they were his messages
            // while this is true, its looks odd.
        } elseif ($user->id == $this->from_user_id && is_null($this->from_cooperation_id)) {
            return true;
        }

        return false;
    }

    /**
     * Returns the opposite from isMyMessage().
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
     * Get the private message views.
     */
    public function privateMessageViews(): HasMany
    {
        return $this->hasMany(PrivateMessageView::class);
    }
}
