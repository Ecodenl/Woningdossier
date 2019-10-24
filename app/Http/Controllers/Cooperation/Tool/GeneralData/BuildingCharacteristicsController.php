<?php

namespace App\Http\Controllers\Cooperation\Tool\GeneralData;

use App\Helpers\HoomdossierSession;
use App\Helpers\StepHelper;
use App\Models\BuildingType;
use App\Models\Cooperation;
use App\Models\EnergyLabel;
use App\Models\ExampleBuilding;
use App\Models\RoofType;
use App\Models\Step;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BuildingCharacteristicsController extends Controller
{
    public function index(Cooperation $cooperation)
    {
        $building = HoomdossierSession::getBuilding(true);
        $buildingOwner = $building->user;

        $buildingTypes = BuildingType::all();
        $roofTypes = RoofType::all();
        $energyLabels = EnergyLabel::where('country_code', 'nl')->get();

        $buildingType = $building->getBuildingType(HoomdossierSession::getInputSource(true));
        $exampleBuildings = collect([]);
        if ($buildingType instanceof BuildingType) {
            $exampleBuildings = ExampleBuilding::forMyCooperation()
                ->where('building_type_id', '=', $buildingType->id)
                ->get();
        }

        $myBuildingFeatures = $building->buildingFeatures()->forMe()->get();

        return view('cooperation.tool.general-data.building-characteristics.index', compact(
            'building', 'buildingOwner', 'buildingTypes', 'energyLabels', 'roofTypes', 'exampleBuildings', 'myBuildingFeatures'
        ));
    }
}
