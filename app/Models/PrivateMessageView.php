<?php

namespace App\Models;

use App\Services\BuildingCoachStatusService;
use Illuminate\Database\Eloquent\Attributes\Scope;
use App\Observers\PrivateMessageViewObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use App\Helpers\Hoomdossier;
use App\Helpers\HoomdossierSession;
use App\Traits\GetMyValuesTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\PrivateMessageView
 *
 * @property int $id
 * @property int $private_message_id
 * @property int|null $user_id
 * @property int|null $input_source_id
 * @property int|null $to_cooperation_id
 * @property \Illuminate\Support\Carbon|null $read_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\InputSource|null $inputSource
 * @method static Builder<static>|PrivateMessageView allInputSources()
 * @method static Builder<static>|PrivateMessageView forBuilding(\App\Models\Building|int $building)
 * @method static Builder<static>|PrivateMessageView forCurrentInputSource()
 * @method static Builder<static>|PrivateMessageView forInputSource(\App\Models\InputSource $inputSource)
 * @method static Builder<static>|PrivateMessageView forMe(?\App\Models\User $user = null)
 * @method static Builder<static>|PrivateMessageView forUser(\App\Models\User|int $user)
 * @method static Builder<static>|PrivateMessageView newModelQuery()
 * @method static Builder<static>|PrivateMessageView newQuery()
 * @method static Builder<static>|PrivateMessageView query()
 * @method static Builder<static>|PrivateMessageView residentInput()
 * @method static Builder<static>|PrivateMessageView whereCreatedAt($value)
 * @method static Builder<static>|PrivateMessageView whereId($value)
 * @method static Builder<static>|PrivateMessageView whereInputSourceId($value)
 * @method static Builder<static>|PrivateMessageView wherePrivateMessageId($value)
 * @method static Builder<static>|PrivateMessageView whereReadAt($value)
 * @method static Builder<static>|PrivateMessageView whereToCooperationId($value)
 * @method static Builder<static>|PrivateMessageView whereUpdatedAt($value)
 * @method static Builder<static>|PrivateMessageView whereUserId($value)
 * @mixin \Eloquent
 */
#[ObservedBy([PrivateMessageViewObserver::class])]
class PrivateMessageView extends Model
{
    use GetMyValuesTrait;

    protected $fillable = [
        'input_source_id', 'private_message_id', 'user_id', 'to_cooperation_id', 'read_at',
    ];

    protected function casts(): array
    {
        return [
            'read_at' => 'datetime',
        ];
    }

    /**
     * Get the total unread messages for a user within its given cooperation and after a specific date.
     *
     * @param  $specificDate
     */
    public static function getTotalUnreadMessagesForUserAndCooperationAfterSpecificDate(
        User $user,
        Cooperation $cooperation,
        $specificDate
    ): int
    {
        $cooperationUnreadMessagesCount = 0;

        // if the user has the role coordinator or cooperation-admin get them as well
        if ($user->hasRole(['coordinator', 'cooperation-admin'])) {
            $cooperationUnreadMessagesCount = self::where('to_cooperation_id', $cooperation->id)
                ->where('created_at', '>=', $specificDate)
                ->where('read_at', null)
                ->count();
        }

        // get the unread messages for the user itself within its given cooperation after a given date.
        $userUnreadMessages = static::select('private_messages.*')
            ->where('private_message_views.user_id', $user->id)
            ->where('read_at', null)
            ->where('private_message_views.created_at', '>=', $specificDate)
            ->join('private_messages', function ($query) {
                $query->on('private_message_views.private_message_id', '=', 'private_messages.id');
            })->count();

        return $userUnreadMessages + $cooperationUnreadMessagesCount;
    }

    /**
     * Query to scope records for the current input source.
     *
     * Normally we would use the GetValueTrait which applies the global GetValueScope.
     *
     * BUT: the input_source_id will sometimes be empty (coordinator and cooperation-admin), so we
     * can't use the global scope.
     */
    #[Scope]
    protected function forCurrentInputSource(Builder $query): Builder
    {
        return $query->where('input_source_id', HoomdossierSession::getInputSourceValue());
    }

    /**
     * Get the total unread messages from a auth user.
     */
    public static function getTotalUnreadMessagesForCurrentRole(bool $splitByPublic = false, array $where = []): int|array
    {
        // Start new query
        return static::query()
            // Join the related private messages to the view.
            ->join('private_messages', function ($query) {
                $query->on('private_message_views.private_message_id', '=', 'private_messages.id');
            })
            ->when(
                // If the user's current role is coordinator or cooperation admin, then he
                // speaks for the cooperation itself, so we need to get the unread messages for the cooperation.
                Hoomdossier::user()->hasRoleAndIsCurrentRole(['coordinator', 'cooperation-admin']),
                function ($query) {
                    $query->whereNull('input_source_id')
                        ->where('private_message_views.to_cooperation_id', HoomdossierSession::getCooperation());
                },
                function ($query) {
                    $query->where('private_message_views.user_id', '=', Hoomdossier::user()->id)
                        ->where('input_source_id', '=', HoomdossierSession::getInputSource());
                }
            )
            // Where not read
            ->where('read_at', null)
            // Apply custom wheres to scope the unread count to a specific section, such as a building
            ->when(! empty($where), fn ($q) => $q->where($where))
            // Finally, if split, we want to fetch the count grouped by private and public messages. Else we
            // apply a normal count. NOTE: This must be an anonymous query, else it remains wrapped as query.
            ->when(
                $splitByPublic,
                fn ($q) => $q->selectRaw('is_public, COUNT(*) as total')
                    ->groupBy('is_public')->pluck('total', 'is_public')->all(),
                fn ($q) => $q->count()
            );
    }

    /**
     * Get the unread messages count for a given building.
     * The count will be determined on the auth user's role and user id.
     */
    public static function getTotalUnreadMessagesCountByBuildingForAuthUser(Building $building): int
    {
        // get all the private message id's for a building
        $privateMessageIdsForBuilding = $building->privateMessages
            ->pluck('id')
            ->all();

        // get the unread messages for the cooperation
        if (Hoomdossier::user()->hasRoleAndIsCurrentRole(['coordinator', 'cooperation-admin'])) {
            return static::where('to_cooperation_id', HoomdossierSession::getCooperation())
                ->whereIn('private_message_id', $privateMessageIdsForBuilding)
                ->whereNull('read_at')
                ->count();
        }

        // Coach and resident: check user_id + input_source_id
        return static::where('user_id', Hoomdossier::user()->id)
            ->forCurrentInputSource()
            ->whereIn('private_message_id', $privateMessageIdsForBuilding)
            ->whereNull('read_at')
            ->count();
    }
}
