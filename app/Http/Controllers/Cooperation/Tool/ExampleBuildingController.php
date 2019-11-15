<?php

namespace App\Http\Controllers\Cooperation\Tool;

use App\Helpers\HoomdossierSession;
use App\Models\ExampleBuilding;
use App\Services\ExampleBuildingService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ExampleBuildingController extends Controller
{
    public function apply(Request $request)
    {
        $building = HoomdossierSession::getBuilding(true);
        $buildYear = $request->get('build_year');

        $exampleBuilding = ExampleBuilding::findOrFail(
            $request->get('example_building_id')
        );


        ExampleBuildingService::apply($exampleBuilding, $buildYear, $building);

        // we need to associate the example building with it after it has been applied since we will do a check in the ToolSettingTrait on the example_building_id
        $building->exampleBuilding()->associate($exampleBuilding);
        $building->save();

        return response()->json();
    }
}
