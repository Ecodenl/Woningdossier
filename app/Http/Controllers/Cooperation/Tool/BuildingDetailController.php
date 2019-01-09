<?php

namespace App\Http\Controllers\Cooperation\Tool;

use App\Helpers\HoomdossierSession;
use App\Helpers\Str;
use App\Http\Requests\BuildingDetailRequest;
use App\Models\Building;
use App\Models\BuildingFeature;
use App\Models\BuildingType;
use App\Models\Cooperation;
use App\Models\ExampleBuilding;
use App\Models\Step;
use App\Scopes\GetValueScope;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BuildingDetailController extends Controller
{
    public function index(Request $request, Cooperation $cooperation)
    {
        $building = Building::find(HoomdossierSession::getBuilding());
        $buildingTypes = BuildingType::all();

        return view('cooperation.tool.building-detail.index', compact('building', 'buildingTypes'));
    }

    public function store(BuildingDetailRequest $request)
    {
        /** @var Building $building */
        $building = Building::find(HoomdossierSession::getBuilding());
        $buildingId = $building->id;
        $inputSourceId = HoomdossierSession::getInputSource();



        $features = BuildingFeature::withoutGlobalScope(GetValueScope::class)->updateOrCreate(
            [
                'building_id' => $buildingId,
                'input_source_id' => $inputSourceId,
            ],
            [
                'build_year' => $request->get('build_year'),
            ]
        );

        $buildingType = BuildingType::find($request->get('building_type_id'));
        dd($this->exampleBuildingType($buildingType));
        $features->buildingType()->associate($buildingType);

        $exampleBuilding = $this->exampleBuildingType($buildingType);
        if ($exampleBuilding instanceof ExampleBuilding) {
            $building->exampleBuilding()->associate($exampleBuilding);
            $building->save();
        }

    }

    public function exampleBuildingType($buildingTypeId)
    {
        $exampleBuildings = ExampleBuilding::forMyCooperation()->buildingsByBuildingType($buildingTypeId)->get();
        // loop through all the example buildings so we can add the "real name" to the examplebuilding
        foreach ($exampleBuildings as $exampleBuilding) {
            $exampleBuildings->where('id', $exampleBuilding->id)->first()->real_name = $exampleBuilding->name;
        }

        return response()->json($exampleBuildings);
    }
}
