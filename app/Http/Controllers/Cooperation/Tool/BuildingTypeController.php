<?php

namespace App\Http\Controllers\Cooperation\Tool;

use App\Helpers\HoomdossierSession;
use App\Http\Requests\Cooperation\Tool\BuildingTypeFormRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BuildingTypeController extends Controller
{
    /**
     * Store the bulding type id, when a user changes his building type id
     * after that selects a example building, the page will be reloaded.
     * but the type wasnt stored. now it is
     *
     * @param BuildingTypeFormRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(BuildingTypeFormRequest $request)
    {
        $buildingTypeId = $request->get('building_type_id');
        $buildYear = $request->get('build_year');
        $building = HoomdossierSession::getBuilding(true);
        $inputSource = HoomdossierSession::getInputSource(true);
        $building->buildingFeatures()->updateOrCreate(
            [
                'input_source_id' => $inputSource->id
            ],
            [
                'building_type_id' => $buildingTypeId,
                'build_year' => $buildYear,
            ]
        );

        return response()->json();
    }

}
