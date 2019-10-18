<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * App\Models\BuildingCoachStatus.
 *
 * @property int                             $id
 * @property int|null                        $coach_id
 * @property int|null                        $building_id
 * @property string                          $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \App\Models\Building|null       $building
 * @property \App\Models\User|null           $coach
 * @property \App\Models\User|null           $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingCoachStatus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingCoachStatus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingCoachStatus query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingCoachStatus whereBuildingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingCoachStatus whereCoachId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingCoachStatus whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingCoachStatus whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingCoachStatus whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingCoachStatus whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class BuildingCoachStatus extends Model
{
    protected $table = 'building_coach_statuses';

    protected $fillable = [
        'coach_id', 'status', 'building_id',
    ];

    // status will be set when a coach is added to a building
    const STATUS_ADDED = 'added';

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
     * Returns the 'connected' coaches from a given building id.
     * A coach is considered to be connected when he has more pending statuses then removed statuses.
     *
     * @param int $buildingId
     *
     * @return \Illuminate\Support\Collection
     */
    public static function getConnectedCoachesByBuildingId(int $buildingId): Collection
    {
        $pendingCount = \DB::raw('(
                SELECT coach_id, building_id, count(`status`) AS count_pending
	            FROM building_coach_statuses
	            WHERE coach_id is not null
	            AND building_id = '.$buildingId.' 
	            AND `coach_id` IS NOT NULL 
	            AND `status` = \''.BuildingCoachStatus::STATUS_ADDED.' \'
	            group by coach_id, building_id
            )  AS bcs2');
        $removedCount = \DB::raw('(
                SELECT building_id, coach_id, count(`status`) AS count_removed
	            FROM building_coach_statuses
	            
	            WHERE coach_id is not null
	            AND building_id = '.$buildingId.' 
	            AND `coach_id` IS NOT NULL 
	            AND `status` = \''.BuildingCoachStatus::STATUS_REMOVED.' \'
	            group by coach_id, building_id
            ) AS bcs3');
        $buildingPermissionCount = \DB::raw('(
                SELECT user_id, count(`building_id`) as count_building_permission
	            FROM building_permissions
	            WHERE building_id = '.$buildingId.'
	            GROUP BY user_id
            ) as bp');

        /**
         * Retrieves the coaches that have a pending building status, also returns the building_permission count so we can check if the coach can access the building.
         */
        $coachesWithPendingBuildingCoachStatus =
            \DB::query()->select('bcs2.coach_id', 'bcs2.building_id', 'bcs2.count_pending AS count_pending',
                'bcs3.count_removed AS count_removed', 'bp.count_building_permission as count_building_permission')
               ->from($pendingCount)
               ->leftJoin($removedCount, 'bcs2.coach_id', '=', 'bcs3.coach_id')
               ->leftJoin($buildingPermissionCount, 'bcs2.coach_id', '=', 'bp.user_id')
               ->where('bcs3.coach_id', '!=', null)
                ->whereRaw('(count_pending > count_removed) OR count_removed IS NULL')
               ->get();

        return $coachesWithPendingBuildingCoachStatus;
    }

    /**
     * Returns all the connected buildings from a user (coach).
     *
     * @param User        $user,        the user we want the connected buildings from
     * @param Cooperation $cooperation, from which cooperation we want to retrieve it
     *
     * @return Collection
     */
    public static function getConnectedBuildingsByUser(User $user, Cooperation $cooperation): Collection
    {
        $userId = $user->id;
        $cooperationId = $cooperation->id;

        $pendingCount = \DB::raw('(
                SELECT coach_id, building_id, count(`status`) AS count_pending
	            FROM building_coach_statuses
	            WHERE coach_id = '.$userId.' AND `status` = \''.BuildingCoachStatus::STATUS_ADDED.' \'
	            group by coach_id, building_id
            )  AS bcs2');
        $removedCount = \DB::raw('(
                SELECT building_id, coach_id, count(`status`) AS count_removed
	            FROM building_coach_statuses
	            WHERE coach_id = '.$userId.' AND `status` = \''.BuildingCoachStatus::STATUS_REMOVED.' \'
	            group by coach_id, building_id
            ) AS bcs3');
        $buildingPermissionCount = \DB::raw('(
                SELECT user_id, count(`building_id`) as count_building_permission
	            FROM building_permissions
	            WHERE user_id = '.$userId.'
	            GROUP BY user_id
            ) as bp');

        // query to get the buildings a user is connected to
        $buildingsTheCoachIsConnectedTo =
            \DB::query()->select('bcs2.coach_id', 'bcs2.building_id', 'bcs2.count_pending AS count_pending',
                'bcs3.count_removed AS count_removed', 'bp.count_building_permission as count_building_permission',
                // accept from the cooperation-building-link
                'users.cooperation_id')
                // count the pending statuses
               ->from($pendingCount)
                // count the removed count
               ->leftJoin($removedCount, 'bcs2.building_id', '=', 'bcs3.building_id')
                // check the building permissions
               ->leftJoin($buildingPermissionCount, 'bcs2.coach_id', '=', 'bp.user_id')
                // get the buildings
               ->leftJoin('buildings', 'bcs2.building_id', '=', 'buildings.id')
                // check if the building its user / resident is associated with the given cooperation

                // accept from the cooperation-building-link
               ->join('users', function ($joinUsers) use ($cooperationId) {
                   $joinUsers->on('buildings.user_id', '=', 'users.id')
                                       ->where('cooperation_id', $cooperationId);
               })
                // check if the coach has access
               ->whereRaw('(count_pending > count_removed) OR count_removed IS NULL')
               ->where('buildings.deleted_at', '=', null)
                // accept from the cooperation-building-link
               ->groupBy('building_id', 'users.cooperation_id', 'coach_id', 'count_removed', 'count_pending', 'count_building_permission')
               ->get();

        return $buildingsTheCoachIsConnectedTo;
    }

    /**
     * Get the current status for a given building id, can return the translation or the status key
     * will return the translation by default.
     *
     * @param int  $buildingId
     * @param bool $returnTranslation
     *
     * @return string
     */
    public static function getCurrentStatusForBuildingId(int $buildingId, bool $returnTranslation = true): string
    {
        \Illuminate\Support\Facades\Log::debug(__METHOD__.' is still being used, remove it as soon as possible.');
        // get the building, even if its deleted.
        $building = Building::withTrashed()->find($buildingId);

        if ($building instanceof Building) {
            return $building->getMostRecentBuildingStatus()->status->name;
        }

        return '';
    }
}
