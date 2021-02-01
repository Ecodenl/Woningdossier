<?php

namespace App\Http\Controllers\Cooperation\Admin\Cooperation;

use App\Http\Controllers\Controller;
use App\Models\Building;
use App\Models\Cooperation;
use App\Models\Role;
use App\Models\User;
use App\Services\BuildingCoachStatusService;

class CoachController extends Controller
{
    /**
     * Show all the coaches and coordinators.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Cooperation $cooperation)
    {
        $users = $cooperation
            ->users()
            ->role(['coach', 'coordinator'])
            ->get();

        $roles = Role::all();

        return view('cooperation.admin.cooperation.coaches.index', compact('roles', 'users'));
    }

    /**
     * Show a list of a coach its connected buildings.
     *
     * @param $userId
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Cooperation $cooperation, User $user)
    {
        $userToShow = $user;
        $buildingFromUser = $userToShow->building;

        $connectedBuildingsForUser = BuildingCoachStatusService::getConnectedBuildingsByUser($userToShow)->pluck('building_id');

        // now we got the connected buildings of the user, get the models.
        $buildings = Building::findMany($connectedBuildingsForUser)
            ->load(['user',
                    'buildingStatuses' => function ($q) {
                        $q->with('status')->mostRecent();
                    },
                ]
            );

        $roles = $userToShow->roles->pluck('human_readable_name')->toArray();

        return view('cooperation.admin.cooperation.coaches.show', compact(
            'connectedBuildingsForUser', 'userToShow', 'roles', 'buildingFromUser', 'buildings'
        ));
    }
}
