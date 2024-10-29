<?php

namespace App\Http\Controllers\Cooperation\Admin\Cooperation;

use Illuminate\View\View;
use App\Helpers\RoleHelper;
use App\Http\Controllers\Controller;
use App\Models\Building;
use App\Models\Cooperation;
use App\Models\User;
use App\Services\BuildingCoachStatusService;

class CoachController extends Controller
{
    /**
     * Show all the coaches and coordinators.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Cooperation $cooperation): View
    {
        $users = $cooperation
            ->users()
            ->with('building', 'roles')
            ->role([RoleHelper::ROLE_COACH, RoleHelper::ROLE_COORDINATOR])
            ->get();

        return view('cooperation.admin.cooperation.coaches.index', compact('users'));
    }

    /**
     * Show a list of a coach its connected buildings.
     *
     * @param $userId
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Cooperation $cooperation, User $user): View
    {
        $userToShow = $user;
        $buildingFromUser = $userToShow->building;

        $connectedBuildingsForUser = BuildingCoachStatusService::getConnectedBuildingsByUser($userToShow)->pluck('building_id');

        $buildings = Building::withRecentBuildingStatusInformation()
            ->whereIn('buildings.id', $connectedBuildingsForUser)
            ->orderByDesc('appointment_date')
            ->with('user')
            ->get();

        $buildings = $buildings->pullTranslationFromJson('status_name_json', 'status');

        $roles = $userToShow->roles->pluck('human_readable_name')->toArray();

        return view('cooperation.admin.cooperation.coaches.show', compact(
            'connectedBuildingsForUser', 'userToShow', 'roles', 'buildingFromUser', 'buildings'
        ));
    }
}
