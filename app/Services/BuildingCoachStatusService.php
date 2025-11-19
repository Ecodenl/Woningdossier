<?php

namespace App\Services;

use App\Models\Building;
use App\Models\BuildingCoachStatus;
use App\Models\User;
use App\Services\Models\BuildingStatusService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class BuildingCoachStatusService
{
    /**
     * Add a building coach status with status removed, this will lead to "revoked access".
     */
    public static function revokeAccess(User $user, Building $building): bool
    {
        BuildingCoachStatus::create([
            'coach_id' => $user->id,
            'building_id' => $building->id,
            'status' => BuildingCoachStatus::STATUS_REMOVED,
        ]);

        return true;
    }

    /**
     * Give the user / coach an added building status, which grants him access to messages the resident and add details.
     * This does not give the user permission to access the building!
     */
    public static function giveAccess(User $user, Building $building): bool
    {
        BuildingCoachStatus::create([
            'coach_id' => $user->id,
            'building_id' => $building->id,
            'status' => BuildingCoachStatus::STATUS_ADDED,
        ]);

        app(BuildingStatusService::class)->forBuilding($building)->setStatus('in_progress');

        return true;
    }

    /**
     * Returns all the connected buildings from a coach.
     */
    public static function getConnectedBuildingsByUser(User $user): Collection
    {
        // A building is considered attached to a coach when
        // 1. the building has at least one `added` BuildingCoachStatus
        // 2. the total BuildingCoachStatus with `added` is higher than the ones with `removed`

        //TODO: It would make more sense if this was a logbook to say when a status was added or removed,
        // instead of having each individual trace to be queried upon, or perhaps an idea to only use the last created.

        return $user->buildingCoachStatuses()
            ->selectRaw('building_id, SUM(status = ?) AS added, SUM(status = ?) AS removed', [BuildingCoachStatus::STATUS_ADDED, BuildingCoachStatus::STATUS_REMOVED])
            ->has('building')
            ->groupBy('building_id')
            ->havingRaw('added > removed')
            ->get();
    }

    /**
     * Returns all the connected coaches from a building.
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, \Illuminate\Database\Eloquent\Model> Collection of App\Models\BuildingCoachStatus with properties coach_id, added, removed
     */
    public static function getConnectedCoachesByBuilding(Building $building, bool $eagerLoadCoach = false): \Illuminate\Database\Eloquent\Collection
    {
        return $building->buildingCoachStatuses()
            ->selectRaw('coach_id, SUM(status = ?) AS added, SUM(status = ?) AS removed', [BuildingCoachStatus::STATUS_ADDED, BuildingCoachStatus::STATUS_REMOVED])
            ->has('coach')
            ->groupBy('coach_id')
            ->havingRaw('added > removed')
            // Eager load the coaches if wanted to prevent unnecessary queries
            ->when($eagerLoadCoach, fn (Builder $query) => $query->with(['coach']))
            ->get();
    }
}
