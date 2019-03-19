<?php

namespace App\Http\Controllers\Cooperation\Admin\Coach;

use App\Http\Controllers\Controller;
use App\Models\Building;
use App\Models\BuildingCoachStatus;
use App\Models\BuildingPermission;
use App\Models\Cooperation;
use App\Models\PrivateMessage;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BuildingController extends Controller
{
    public function index()
    {
        $userId = \Auth::id();
        // get most recent building coach statuses for
        $buildingCoachStatuses = BuildingCoachStatus::hydrate(
            \DB::table('building_coach_statuses as bcs1')->select('coach_id', 'building_id', 'created_at', 'status', 'appointment_date')
                ->where('created_at', function ($query) use ($userId) {
                    $query->select(\DB::raw('MAX(created_at)'))
                        ->from('building_coach_statuses as bcs2')
                        ->whereRaw('coach_id = ' . $userId . ' and bcs1.building_id = bcs2.building_id');
                })->where('coach_id', $userId)
                ->orderBy('created_at')
                ->get()->all()
        );


        return view('cooperation.admin.coach.buildings.index', compact('buildings', 'buildingCoachStatuses'));
    }
}
