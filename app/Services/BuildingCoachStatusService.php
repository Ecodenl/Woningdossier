<?php

namespace App\Services;

use App\Models\Building;
use App\Models\BuildingCoachStatus;
use App\Models\Cooperation;
use App\Models\User;
use Illuminate\Support\Collection;

class BuildingCoachStatusService
{
    /**
     * Add a building coach status with status removed, this will lead to "revoked access".
     */
    public static function revokeAccess(User $user, Building $building): bool
    {
        BuildingCoachStatus::create([
            'coach_id' => $user->id, 'building_id' => $building->id, 'status' => BuildingCoachStatus::STATUS_REMOVED,
        ]);

        return true;
    }

    /**
     * Give the user / coach a added building status, which grants him access to messages the resident and add details.
     * does not give the user permission to access the building.
     */
    public static function giveAccess(User $user, Building $building): bool
    {
        // Add the user with status added
        BuildingCoachStatus::create([
            'coach_id' => $user->id,
            'building_id' => $building->id,
            'status' => BuildingCoachStatus::STATUS_ADDED,
        ]);

        $building->setStatus('in_progress');

        return true;
    }

    /**
     * Returns all the connected buildings from a user (coach).
     *
     * @param User        $user        ,        the user we want the connected buildings from
     * @param Cooperation $cooperation , from which cooperation we want to retrieve it
     */
    public static function getConnectedBuildingsByUser(User $user): Collection
    {
        $userId = $user->id;

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

        // query to get the buildings a user is connected to
        $buildingsTheCoachIsConnectedTo =
            \DB::query()->select([
                'bcs2.coach_id',
                'bcs2.building_id',
                'bcs2.count_pending AS count_pending',
                'bcs3.count_removed AS count_removed'
            ])
                // count the pending statuses
                ->from($pendingCount)
                // count the removed count
                ->leftJoin($removedCount, 'bcs2.building_id', '=', 'bcs3.building_id')
                // get the buildings
                ->leftJoin('buildings', 'bcs2.building_id', '=', 'buildings.id')
                // check if the coach has access
                ->whereRaw('(count_pending > count_removed) OR count_removed IS NULL')
                ->where('buildings.deleted_at', '=', null)
                // accept from the cooperation-building-link
                ->groupBy(['building_id',  'coach_id', 'count_removed', 'count_pending'])
                ->get();

        return $buildingsTheCoachIsConnectedTo;
    }


    /**
     * Returns the 'connected' coaches from a given building id.
     * A coach is considered to be connected when he has more pending statuses then removed statuses.
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



}
