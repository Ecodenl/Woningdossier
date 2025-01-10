<?php

namespace App\Http\Controllers\Cooperation\Admin\Coach;

use Illuminate\View\View;
use App\Helpers\Hoomdossier;
use App\Http\Controllers\Controller;
use App\Models\Building;
use App\Services\BuildingCoachStatusService;

class BuildingController extends Controller
{
    public function index(): View
    {
        $connectedBuildingsForUser = BuildingCoachStatusService::getConnectedBuildingsByUser(
            Hoomdossier::user()
        )->pluck('building_id');

        $buildings = Building::withRecentBuildingStatusInformation()
            ->whereIn('buildings.id', $connectedBuildingsForUser)
            ->orderByDesc('appointment_date')
            ->with('user')
            ->get();

        $buildings = $buildings->pullTranslationFromJson('status_name_json', 'status');

        return view('cooperation.admin.coach.buildings.index', compact('buildings', 'buildings'));
    }
}
