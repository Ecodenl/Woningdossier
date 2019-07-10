<?php

namespace App\Models;

use App\Helpers\HoomdossierSession;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;

/**
 * App\Models\BuildingCoachStatus
 *
 * @property int $id
 * @property int|null $coach_id
 * @property int|null $building_id
 * @property int|null $private_message_id
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $appointment_date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Building|null $building
 * @property-read \App\Models\User|null $coach
 * @property-read \App\Models\User|null $user
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
     * @param  int  $buildingId
     *
     * @return \Illuminate\Support\Collection
     */
    public static function getConnectedCoachesByBuildingId(int $buildingId): Collection
    {
        $pendingCount            = \DB::raw('(
                SELECT coach_id, building_id, count(`status`) AS count_pending
	            FROM building_coach_statuses
	            WHERE coach_id is not null
	            AND building_id = '.$buildingId.' AND `status` = \''.BuildingCoachStatus::STATUS_ADDED.' \'
	            group by coach_id, building_id
            )  AS bcs2');
        $removedCount            = \DB::raw('(
                SELECT building_id, coach_id, count(`status`) AS count_removed
	            FROM building_coach_statuses
                WHERE coach_id is not null
	            AND building_id = '.$buildingId.' AND `status` = \''.BuildingCoachStatus::STATUS_REMOVED.' \'
	            group by coach_id, building_id
            ) AS bcs3');
        $buildingPermissionCount = \DB::raw('(
                SELECT user_id, count(`building_id`) as count_building_permission
	            FROM building_permissions
	            WHERE building_id = '.$buildingId.'
	            GROUP BY user_id
            ) as bp');


        /**
         * Retrieves the coaches that have a pending building status, also returns the building_permission count so we can check if the coach can access the building
         */
        $coachesWithPendingBuildingCoachStatus =
            \DB::query()->select('bcs2.coach_id', 'bcs2.building_id', 'bcs2.count_pending AS count_pending',
                'bcs3.count_removed AS count_removed', 'bp.count_building_permission as count_building_permission')
               ->from($pendingCount)
               ->leftJoin($removedCount, 'bcs2.coach_id', '=', 'bcs3.coach_id')
               ->leftJoin($buildingPermissionCount, 'bcs2.coach_id', '=', 'bp.user_id')
               ->whereRaw('(count_pending > count_removed) OR count_removed IS NULL')
               ->get();

        return $coachesWithPendingBuildingCoachStatus;
    }



    /**
     * Returns all the connected buildings from a user (coach)
     *
     * @param  User         $user, the user we want the connected buildings from.
     * @param  Cooperation  $cooperation, from which cooperation we want to retrieve it.
     *
     * @return Collection
     */
    public static function getConnectedBuildingsByUser(User $user, Cooperation $cooperation): Collection
    {
        $userId        = $user->id;
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
                'cooperation_user.cooperation_id')
                // count the pending statuses
               ->from($pendingCount)
                // count the removed count
               ->leftJoin($removedCount, 'bcs2.building_id', '=', 'bcs3.building_id')
                // check the building permissions
               ->leftJoin($buildingPermissionCount, 'bcs2.coach_id', '=', 'bp.user_id')
                // get the buildings
               ->leftJoin('buildings', 'bcs2.building_id', '=', 'buildings.id')
                // check if the building its user / resident is associated with the given cooperation
               ->join('cooperation_user', function ($joinCooperationUser) use ($cooperationId) {
                   $joinCooperationUser->on('buildings.user_id', '=', 'cooperation_user.user_id')
                                       ->where('cooperation_id', $cooperationId);
               })
                // check if the coach has access
               ->whereRaw('(count_pending > count_removed) OR count_removed IS NULL')
               ->where('buildings.deleted_at', '=', null)
               ->groupBy('building_id', 'cooperation_user.cooperation_id', 'coach_id', 'count_removed', 'count_pending', 'count_building_permission')
               ->get();


        return $buildingsTheCoachIsConnectedTo;
    }

}
