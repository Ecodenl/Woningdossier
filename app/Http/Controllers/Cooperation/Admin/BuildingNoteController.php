<?php

namespace App\Http\Controllers\Cooperation\Admin;

use App\Http\Requests\Cooperation\Admin\BuildingNoteRequest;
use App\Models\BuildingNotes;
use App\Models\Cooperation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BuildingNoteController extends Controller
{
    protected $fragment;

    public function __construct(Cooperation $coodperation, Request $request)
    {
        if ($request->has('fragment')) {
            $this->fragment = $request->get('fragment');
        }
    }

    public function store(BuildingNoteRequest $request)
    {
        $note = $request->input('building.note');
        $buildingId = $request->input('building.id');

        BuildingNotes::create([
            'note' => $note,
            'building_id' => $buildingId,
            'coach_id' => \Auth::id(),
        ]);

        return redirect(back()->getTargetUrl().$this->fragment);
    }
}
