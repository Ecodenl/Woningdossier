<?php

namespace App\Http\Controllers\Cooperation\Admin\Cooperation;

use App\Helpers\Hoomdossier;
use App\Models\BuildingCoachStatus;
use App\Models\Cooperation;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CoachController extends Controller
{
    /**
     * Show all the coaches and coordinators
     *
     * @param Cooperation $cooperation
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Cooperation $cooperation)
    {
        $users = $cooperation
            ->users()
            ->role(['coach', 'coordinator'])
            ->where('id', '!=', Hoomdossier::user()->id)
            ->get();


        $roles = Role::all();

        return view('cooperation.admin.cooperation.coaches.index', compact('roles', 'users'));
    }

    /**
     * Show a list of a coach its connected buildings
     *
     * @param  Cooperation  $cooperation
     * @param $userId
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Cooperation $cooperation, $userId)
    {

        $userToShow = User::findOrFail($userId);
        $buildingFromUser = $userToShow->building;

        $buildingCoachStatuses = BuildingCoachStatus::where('coach_id', $userId)
            ->whereHas('building')
            ->with(['building.buildingStatuses' => function ($query) {
                $query->mostRecent();
            }])
            ->groupBy(['building_id', 'coach_id'])
            ->select(['building_id', 'coach_id'])
            ->get();



        $roles = $userToShow->roles->pluck('human_readable_name')->toArray();

        return view('cooperation.admin.cooperation.coaches.show', compact('buildingCoachStatuses', 'userToShow', 'roles', 'buildingFromUser'));
    }
}
