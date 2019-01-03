<?php

namespace App\Http\Controllers\Cooperation\Admin\Coach;

use App\Models\Building;
use App\Models\BuildingCoachStatus;
use App\Models\BuildingNotes;
use App\Models\Cooperation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BuildingDetailsController extends Controller
{
    public function index(Cooperation $cooperation, $buildingId)
    {
//        $buildingNotes = BuildingNotes::where('building_id', $buildingId)->where('coach_id', \Auth::id())->orderByDesc('updated_at')->get();
        // get the building notes from a specific building
        $buildingNotes = \Auth::user()->buildingNotes()->where('building_id', $buildingId)->orderByDesc('updated_at')->get();
        // get the matching building
        $building = Building::withTrashed()->find($buildingId);



        return view('cooperation.admin.coach.buildings.details.index', compact('buildingNotes', 'building'));
    }

    public function store(Request $request)
    {
        $note = strip_tags($request->get('note'));
        $buildingId = $request->get('building_id');


        BuildingNotes::create([
            'note' => $note,
            'coach_id' => \Auth::id(),
            'building_id' => $buildingId,
        ]);

        return redirect()->back();
    }
}
