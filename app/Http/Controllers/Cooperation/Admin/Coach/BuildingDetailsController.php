<?php

namespace App\Http\Controllers\Cooperation\Admin\Coach;

use App\Helpers\Hoomdossier;
use App\Http\Controllers\Controller;
use App\Models\BuildingNotes;
use Illuminate\Http\Request;

class BuildingDetailsController extends Controller
{
    public function store(Request $request)
    {
        $note = strip_tags($request->get('note'));
        $buildingId = $request->get('building_id');

        BuildingNotes::create([
            'note' => $note,
            'coach_id' => Hoomdossier::user()->id,
            'building_id' => $buildingId,
        ]);

        return redirect()->back();
    }
}
