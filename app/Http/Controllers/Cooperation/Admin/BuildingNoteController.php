<?php

namespace App\Http\Controllers\Cooperation\Admin;

use App\Helpers\Hoomdossier;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cooperation\Admin\BuildingNoteRequest;
use App\Models\BuildingNotes;

class BuildingNoteController extends Controller
{
    /**
     * Method to store a note for a building.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(BuildingNoteRequest $request)
    {
        $note = $request->input('building.note');
        $buildingId = $request->input('building.id');

        BuildingNotes::create([
            'note' => $note,
            'building_id' => $buildingId,
            'coach_id' => Hoomdossier::user()->id,
        ]);

        return redirect()->back()->with('fragment', $request->get('fragment'));
    }
}
