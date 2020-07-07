<?php

namespace App\Http\Controllers\Cooperation\Tool;

use App\Helpers\HoomdossierSession;
use App\Models\BuildingType;
use App\Models\ExampleBuilding;
use App\Services\ExampleBuildingService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class ExampleBuildingController extends Controller
{
    public function store(Request $request)
    {
        $building = HoomdossierSession::getBuilding(true);
        $buildYear = $request->get('build_year');
        $buildingTypeId = $request->get('building_type_id');
        $exampleBuildingId = $request->get('example_building_id');

        // when no example building is provided we will try to obtain a generic example type based on the building type
        if (empty($exampleBuildingId)) {
            $buildingType = BuildingType::find($buildingTypeId);
            $exampleBuilding = ExampleBuilding::generic()->where('building_type_id', $buildingType->id)->first();
        } else {
            $exampleBuilding = ExampleBuilding::find($exampleBuildingId);
        }

        if ($exampleBuilding instanceof ExampleBuilding) {
            ExampleBuildingService::apply($exampleBuilding, $buildYear, $building);

            // we need to associate the example building with it after it has been applied since we will do a check in the ToolSettingTrait on the example_building_id
            $building->exampleBuilding()->associate($exampleBuilding);
            $building->save();
        } else {
            ExampleBuildingService::clearExampleBuilding($building);
            Log::debug(__CLASS__. "NO example building is found, clearing for the building. (id: {$building->id})");
        }

        return response()->json();
    }
}
