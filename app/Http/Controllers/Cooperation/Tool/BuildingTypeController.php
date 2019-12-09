<?php

namespace App\Http\Controllers\Cooperation\Tool;

use App\Helpers\HoomdossierSession;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BuildingTypeController extends Controller
{
    /**
     * Store the bulding type id, when a user changes his building type id
     * after that selects a example building, the page will be reloaded.
     * but the type wasnt stored. now it is
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $buildingTypeId = $request->get('building_type_id');
        $buildYear = $request->get('build_year');
        $building = HoomdossierSession::getBuilding(true);
        $building->buildingFeatures()->updateOrCreate([], [
            'building_type_id' => $buildingTypeId,
            'build_year' => $buildYear,
        ]);

        return response()->json();
    }

}
