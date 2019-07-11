<?php

namespace App\Http\Controllers\Cooperation\Admin\Cooperation;

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
            ->where('id', '!=', \Auth::id())
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
        $buildingFromUser = $userToShow->buildings()->first();

        $buildingCoachStatuses = BuildingCoachStatus::where('coach_id', $userId)
            ->whereHas('building')
            ->with(['building' => function ($query) {
                $query->with(['buildingStatuses' => function ($query) {
                    $query->orderByDesc('created_at');
                }]);
            }])
            ->groupBy(['building_id', 'coach_id'])
            ->select(['building_id', 'coach_id'])
            ->get();

//        dd($bcs);
//
//        $buildingCoachStatuses = BuildingCoachStatus::hydrate(
//            \DB::table('building_coach_statuses as bcs1')->select('coach_id', 'building_id', 'created_at')
//                ->where('created_at', function ($query) use ($userId) {
//                    $query->select(\DB::raw('MAX(created_at)'))
//                        ->from('building_coach_statuses as bcs2')
//                        ->whereRaw('coach_id = ' . $userId . ' and bcs1.building_id = bcs2.building_id');
//                })->where('coach_id', $userId)
//                ->orderBy('created_at')
//                ->get()->all()
//        );


//        dd($buildingCoachStatuses);
        $roles = $userToShow->roles->pluck('human_readable_name')->toArray();

        return view('cooperation.admin.cooperation.coaches.show', compact('buildingCoachStatuses', 'userToShow', 'roles', 'buildingFromUser'));
    }
}
