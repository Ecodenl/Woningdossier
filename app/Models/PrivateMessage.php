<?php

namespace App\Models;

use App\Helpers\RoleHelper;
use Illuminate\Database\Eloquent\Attributes\Scope;
use App\Observers\PrivateMessageObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Helpers\Hoomdossier;
use App\Helpers\HoomdossierSession;
use App\Services\BuildingCoachStatusService;
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
 * @property string $message
 * @property int|null $from_user_id
 * @property int|null $from_cooperation_id
 * @property int|null $to_cooperation_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Building|null $building
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PrivateMessageView> $privateMessageViews
 * @property-read int|null $private_message_views_count
 * @method static Builder<static>|PrivateMessage conversation(int $buildingId)
 * @method static Builder<static>|PrivateMessage forMyCooperation()
 * @method static Builder<static>|PrivateMessage myPrivateMessages()
 * @method static Builder<static>|PrivateMessage newModelQuery()
 * @method static Builder<static>|PrivateMessage newQuery()
 * @method static Builder<static>|PrivateMessage private()
 * @method static Builder<static>|PrivateMessage public()
 * @method static Builder<static>|PrivateMessage query()
 * @method static Builder<static>|PrivateMessage whereBuildingId($value)
 * @method static Builder<static>|PrivateMessage whereCreatedAt($value)
 * @method static Builder<static>|PrivateMessage whereFromCooperationId($value)
 * @method static Builder<static>|PrivateMessage whereFromUser($value)
 * @method static Builder<static>|PrivateMessage whereFromUserId($value)
 * @method static Builder<static>|PrivateMessage whereId($value)
 * @method static Builder<static>|PrivateMessage whereIsPublic($value)
 * @method static Builder<static>|PrivateMessage whereMessage($value)
 * @method static Builder<static>|PrivateMessage whereToCooperationId($value)
 * @method static Builder<static>|PrivateMessage whereUpdatedAt($value)
 * @mixin \Eloquent
 */
#[ObservedBy([PrivateMessageObserver::class])]
class PrivateMessage extends Model
{
    protected $fillable = [
        'message', 'from_user_id', 'cooperation_id', 'from_cooperation_id', 'to_cooperation_id',
        'building_id', 'from_user', 'is_public',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_public'    => 'boolean',
        ];
    }

    #[Scope]
    protected function forMyCooperation(Builder $query): Builder
    {
        return $query->where('to_cooperation_id', HoomdossierSession::getCooperation());
    }

    /**
     * Determine if a private message is public.
     */
    public static function isPublic(PrivateMessage $privateMessage): bool
    {
        if ($privateMessage->is_public) {
            return true;
        }

        return false;
    }

    /**
     * Determine if a private message is private.
     */
    public static function isPrivate(PrivateMessage $privateMessage): bool
    {
        return ! self::isPublic($privateMessage);
    }

    /**
     * Scope a query to return the messages that are sent to a user / coach.
     */
    #[Scope]
    protected function myPrivateMessages(Builder $query): Builder
    {
        return $query->where('to_user_id', Hoomdossier::user()->id);
    }

    /**
     * Scope a query to return the conversation ordered on created_at.
     */
    #[Scope]
    protected function conversation(Builder $query, int $buildingId): Builder
    {
        return $query->where('building_id', $buildingId)->orderBy('created_at');
    }

    /**
     * Scope the public messages.
     */
    #[Scope]
    protected function public(Builder $query): Builder
    {
        return $query->where('is_public', true);
    }

    /**
     * Scope the private messages.
     */
    #[Scope]
    protected function private(Builder $query): Builder
    {
        return $query->where('is_public', false);
    }

    public function getSender(): string
    {
        return $this->from_user;
    }

    /**
     * Returns the receiving cooperation of this private message.
     */
    public function getReceivingCooperation(): ?Cooperation
    {
        $receivingCooperationId = $this->to_cooperation_id;
        if (empty($receivingCooperationId)) {
            return null;
        }

        return Cooperation::find($receivingCooperationId);
    }

    /**
     * Returns the receiving cooperation of this private message.
     */
    public function getSendingCooperation(): ?Cooperation
    {
        $sendingCooperationId = $this->from_cooperation_id;
        if (empty($sendingCooperationId)) {
            return null;
        }

        return Cooperation::find($sendingCooperationId);
    }

    /**
     * Get all the "group members".
     * Returns a collection of all the participants for a chat from a building.
     */
    public static function getGroupParticipants(?Building $building = null, bool $publicConversation = true): Collection
    {
        // Check if building exists. We do this so we can pass nullable buildings for ease of use.
        if (! $building instanceof Building || ! $building->exists) {
            return collect();
        }

        // All coaches with access to this building are considered a participant
        $groupMembers = BuildingCoachStatusService::getConnectedCoachesByBuilding($building, true);

        // TODO: Bool is always true at this point, deprecate parameter?
        // If it's a public conversation we push the building owner in it
        if ($publicConversation && $building->user instanceof User) {
            $groupMembers->prepend($building->user);
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
        if ($user->hasRoleAndIsCurrentRole([RoleHelper::ROLE_COACH, RoleHelper::ROLE_COORDINATOR, RoleHelper::ROLE_COOPERATION_ADMIN])) {
            return $this->from_cooperation_id == HoomdossierSession::getCooperation();
        }

        return $user->id == $this->from_user_id && is_null($this->from_cooperation_id);
    }

    /**
     * Get the building from a message.
     */
    public function building(): BelongsTo
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
