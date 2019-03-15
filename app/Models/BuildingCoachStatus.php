<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * App\Models\BuildingCoachStatus.
 *
 * @property int $id
 * @property int $coach_id
 * @property int $building_id
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $appointment_date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $private_message_id
 * @property \App\Models\Building $building
 * @property \App\Models\User $coach
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingCoachStatus currentStatus($status)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingCoachStatus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingCoachStatus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingCoachStatus query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingCoachStatus whereAppointmentDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingCoachStatus whereBuildingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingCoachStatus whereCoachId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingCoachStatus whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingCoachStatus whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingCoachStatus wherePrivateMessageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingCoachStatus whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingCoachStatus whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class BuildingCoachStatus extends Model
{

    protected $table = 'building_coach_statuses';

    protected $fillable = [
        'coach_id', 'status', 'building_id', 'appointment_date', 'private_message_id',
    ];

    protected $dates = [
        'appointment_date',
    ];

    // status that wont be set in the database, its just here for reference to the coordinator and cooperation-admin
    // status will be shown when there isn't a message from the user to the cooperation (or any trace of communication to the cooperation)
    const STATUS_ACTIVE = 'active';

    // status used in 2 ways
    // it will be used when a user has sent a conversation request and when a coach get connected to a building
    // we have to do that otherwise we cant make up if a user has access to the building
    const STATUS_PENDING = 'pending';

    // status that will be set in the database
    // will be set by a coach, coordinator or cooperation-admin
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_EXECUTED = 'executed';
    const STATUS_NO_EXECUTION = 'no_execution';


    // when a user is removed from a building
    const STATUS_REMOVED = 'removed';


    /**
     * Get the building from the status.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function building()
    {
        return $this->belongsTo(Building::class);
    }

    /**
     * Get the user / coach from the status this does NOT return the owner from the building.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'coach_id', 'id');
    }

    /**
     * Get the coach from the status.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function coach()
    {
        return $this->belongsTo('App\Models\User', 'coach_id', 'id');
    }

    /**
     * Returns the current status from a coach bases on the given status.
     *
     * @param $status
     *
     * @return Model|null|object|static
     */
    public function scopeCurrentStatus($query, $status)
    {
        return $query->where('status', $status)->where('coach_id', \Auth::id());
    }

    /**
     * Return the translation from a status
     *
     * @param $status
     * @return string
     */
    public static function getTranslationForStatus($status): string
    {
        return __('woningdossier.building-coach-statuses.'.$status);
    }
    /**
     * Return the manageable statuses (the statuses that can be set by a coach, coordinator, cooperation-admin)
     */
    public static function getManageableStatuses()
    {
        return collect([
            static::STATUS_IN_PROGRESS => static::getTranslationForStatus(static::STATUS_IN_PROGRESS ),
            static::STATUS_EXECUTED => static::getTranslationForStatus(static::STATUS_EXECUTED ),
            static::STATUS_NO_EXECUTION => static::getTranslationForStatus(static::STATUS_NO_EXECUTION ),
        ]);
    }

    /**
     * Get the current status for a given building id, can return the translation or the status key
     * will return the translation by default.
     *
     * @param int $buildingId
     * @param bool $returnTranslation
     * @return string
     */
    public static function getCurrentStatusForBuildingId(int $buildingId, bool $returnTranslation = true): string
    {
        // get the building, even if its deleted.
        $building = Building::withTrashed()->find($buildingId);

        $buildingCoachStatuses = static::getConnectedCoachesByBuildingId($buildingId);
        $buildingConversationRequest = PrivateMessage::conversationRequest($buildingId)->first();

        // first we need to check if the building is active
        // this is the base for every status
        // is the building is active, we try to get the building from the building coach status
        // if the building is not active we return the in active status.
        if ($building->isActive()) {

            // check if a coach is connected with the building
            if ($buildingCoachStatuses->isNotEmpty()) {
                $lastKnownBuildingCoachStatus = static::where('status', '!=', BuildingCoachStatus::STATUS_REMOVED)->get()->last();
                // and the status from it
                $status = $lastKnownBuildingCoachStatus->status;
                // get the translation
                $statusTranslation = __('woningdossier.building-coach-statuses.'.$status);

            } else if ($buildingConversationRequest instanceof PrivateMessage) {
                // if a conversation request has been sent, the status is pending.
                $status = static::STATUS_PENDING;
                // get the translation
                $statusTranslation = __('woningdossier.building-coach-statuses.'.$status);

            } else {
                // no coach status and no conversation request, the status is active.
                $status = static::STATUS_ACTIVE;
                // get the translation
                $statusTranslation = __('woningdossier.building-coach-statuses.'.$status);
            }
        } else {
            // see comments in the if statement above.
            $status = 'in-active-status';
            $statusTranslation = __('woningdossier.building.in-active-status');
        }


        if ($returnTranslation) {
            return $statusTranslation;
        } else {
            return $status;
        }
    }

    /**
     * Returns the 'connected' coaches from a given building id.
     * A coach is considered to be connected when he has more pending statuses then removed statuses.
     *
     * @param int $buildingId
     * @return \Illuminate\Support\Collection
     */
    public static function getConnectedCoachesByBuildingId(int $buildingId): Collection
    {
        $pendingCount = \DB::raw('(
                SELECT coach_id, building_id, count(`status`) AS count_pending
	            FROM building_coach_statuses
	            WHERE building_id = ' . $buildingId . ' AND `status` = \'' . BuildingCoachStatus::STATUS_PENDING. ' \'
	            group by coach_id, building_id
            )  AS bcs2');
        $removedCount = \DB::raw('(
                SELECT building_id, coach_id, count(`status`) AS count_removed
	            FROM building_coach_statuses
	            WHERE building_id = ' . $buildingId . ' AND `status` = \'' . BuildingCoachStatus::STATUS_REMOVED . ' \'
	            group by coach_id, building_id
            ) AS bcs3');
        $buildingPermissionCount = \DB::raw('(
                SELECT user_id, count(`building_id`) as count_building_permission
	            FROM building_permissions
	            WHERE building_id = ' . $buildingId . '
	            GROUP BY user_id
            ) as bp');


        /**
         * Retrieves the coaches that have a pending building status, also returns the building_permission count so we can check if the coach can access the building
         */
        $coachesWithPendingBuildingCoachStatus =
            \DB::query()->select('bcs2.coach_id', 'bcs2.building_id', 'bcs2.count_pending AS count_pending', 'bcs3.count_removed AS count_removed', 'bp.count_building_permission as count_building_permission')
                ->from($pendingCount)
                ->leftJoin($removedCount, 'bcs2.coach_id', '=', 'bcs3.coach_id')
                ->leftJoin($buildingPermissionCount, 'bcs2.coach_id', '=', 'bp.user_id')
                ->havingRaw('(count_pending > count_removed) OR count_removed IS NULL')
                ->get();

        return $coachesWithPendingBuildingCoachStatus;
    }

    /**
     * Returns the most recent statuses for a building id grouped on coach id.
     *
     * @NOTE only returns the statuses if the coach is active.
     *
     * @param $buildingId
     * @return Collection
     */
    public static function getMostRecentStatusesForBuildingId($buildingId): Collection
    {
        $coachesWithActiveBuildingCoachStatus = static::getConnectedCoachesByBuildingId($buildingId);

        // so we can where in on the most recent statuses, so we only get the statuses for the coaches that aren't removed
        $coachIdsThatAreConnectedToBuilding = $coachesWithActiveBuildingCoachStatus->pluck('coach_id')->toArray();

        return \DB::table('building_coach_statuses as bcs1')
            ->select('coach_id', 'building_id', 'created_at', 'status', 'bcs1.appointment_date')
            ->where('created_at', function ($query) use ($buildingId) {
                $query->select(\DB::raw('MAX(created_at)'))
                    ->from('building_coach_statuses as bcs2')
                    ->whereRaw('building_id = '.$buildingId.' and bcs1.coach_id = bcs2.coach_id');
            })
            ->whereIn('coach_id', $coachIdsThatAreConnectedToBuilding)
            ->orderByDesc('created_at')
            ->get();

    }
    /**
     * A function to check if a coach has 'access' to a a building
     * if the active count i higher then the remove count he has 'access'
     * i say 'access' because he cant access the building without a building_permission, however he can access the building details and a groupchat.
     *
     * @param $buildingId
     * @param $coachId
     *
     * @return bool
     */
    public static function hasCoachAccess($buildingId, $coachId): bool
    {
        // count the active statuses
        $buildingCoachStatusActive = self::where('coach_id', '=', $coachId)
            ->where('building_id', $buildingId)
            ->where('status', '=', self::STATUS_ACTIVE)->count();

        // count the removed statuses
        $buildingCoachStatusRemoved = self::where('coach_id', '=', $coachId)
            ->where('building_id', $buildingId)
            ->where('status', '=', self::STATUS_REMOVED)->count();

        if ($buildingCoachStatusActive > $buildingCoachStatusRemoved) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Check if a BuildingCoachStatus row has a appointment_date thats not null.
     *
     * @return bool
     */
    public function hasAppointmentDate(): bool
    {
        return !is_null($this->appointment_date);
    }
}
