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
                ->role('coach')
                ->where('id', '!=', \Auth::id())
                ->get();

        $roles = Role::all();

        return view('cooperation.admin.cooperation.coaches.index', compact('roles', 'users'));
    }

    public function show(Cooperation $cooperation, $userId)
    {
        // retrieve all the coach statuses from the coach.
        $buildingCoachStatuses = BuildingCoachStatus::select('building_id')
//            ->orderByDesc('created_at')
            ->where('coach_id', \Auth::id())
            ->groupBy('building_id')
            ->with(
                ['building' => function($query) {
                    $query->withTrashed()
                        ->with('user');
                }]
            )->get();


        return view('cooperation.admin.cooperation.coaches.show', compact('buildingCoachStatuses'));
    }
}
