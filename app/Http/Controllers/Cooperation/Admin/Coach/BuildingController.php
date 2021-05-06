<?php

namespace App\Http\Controllers\Cooperation\Admin\Coach;

use App\Helpers\Hoomdossier;
use App\Http\Controllers\Controller;
use App\Models\Building;
use App\Services\BuildingCoachStatusService;
use Illuminate\Support\Facades\DB;

class BuildingController extends Controller
{
    public function index()
    {
        $connectedBuildingsForUser = BuildingCoachStatusService::getConnectedBuildingsByUser(
            Hoomdossier::user()
        )->pluck('building_id');


        $recentBuildingStatuses = DB::table('building_statuses')
            ->selectRaw('building_id, max(created_at) as max_created_at, max(id) AS max_id')
            ->groupByRaw('building_id');

        $buildings = Building::select([
            'buildings.*',
            'translations.translation as status_translation',
            'appointment_date',
        ])->leftJoin('building_statuses as bs', 'bs.building_id', '=', 'buildings.id')
            ->rightJoinSub($recentBuildingStatuses, 'bs2', 'bs2.max_id', '=', 'bs.id')
            ->leftJoin('statuses', 'bs.status_id', '=', 'statuses.id')
            ->leftJoin('translations', 'statuses.name', '=', 'translations.key')
            ->whereIn('buildings.id', $connectedBuildingsForUser)
            ->where('translations.language', '=', 'nl')
            ->orderByDesc('appointment_date')
            ->with('user')
            ->get();

        return view('cooperation.admin.coach.buildings.index', compact('buildings', 'buildings'));
    }
}

