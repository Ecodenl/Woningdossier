<?php

namespace App\Http\Controllers\Cooperation\Tool;

use App\Helpers\Cooperation\Tool\RoofInsulationHelper;
use App\Helpers\HoomdossierSession;
use App\Helpers\RoofInsulation;
use App\Helpers\Str;
use App\Http\Requests\Cooperation\Tool\RoofInsulationFormRequest;
use App\Models\Building;
use App\Models\BuildingHeating;
use App\Models\Element;
use App\Models\MeasureApplication;
use App\Models\RoofTileStatus;
use App\Models\RoofType;
use App\Services\ConsiderableService;
use App\Services\StepCommentService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class RoofInsulationController extends ToolController
{
    /**
     * Display a listing of the resource.
     *
     * return \Illuminate\Http\Response
     */
    public function index()
    {
        $typeIds = [5];

        /** var Building $building */
        $building = HoomdossierSession::getBuilding(true);

        /** var BuildingFeature $features */
        $features = $building->buildingFeatures;
        $buildingFeaturesForMe = $building->buildingFeatures()->forMe()->get();

        $primaryRoofTypes = RoofType::orderBy('order')->get();
        $secondaryRoofTypes = $primaryRoofTypes->whereIn('short', RoofType::SECONDARY_ROOF_TYPE_SHORTS);

        $currentRoofTypes = $building->roofTypes;
        $currentRoofTypesForMe = $building->roofTypes()->forMe()->get();

        $roofTileStatuses = RoofTileStatus::orderBy('order')->get();
        $roofInsulation = Element::where('short', 'roof-insulation')->first();
        $heatings = BuildingHeating::all();
        $measureApplications = RoofInsulation::getMeasureApplicationsAdviceMap();

        $currentCategorizedRoofTypes = [
            'flat' => [],
            'pitched' => [],
        ];

        $currentCategorizedRoofTypesForMe = [
            'flat' => [],
            'pitched' => [],
        ];

        if ($currentRoofTypes instanceof Collection) {
            /** var BuildingRoofType $currentRoofType */
            foreach ($currentRoofTypes as $currentRoofType) {
                $cat = RoofInsulation::getRoofTypeCategory($currentRoofType->roofType);
                if (! empty($cat)) {
                    $currentCategorizedRoofTypes[$cat] = $currentRoofType->toArray();
                }
            }

            foreach ($currentRoofTypesForMe as $currentRoofTypeForMe) {
                $cat = RoofInsulation::getRoofTypeCategory($currentRoofTypeForMe->roofType);
                if (! empty($cat)) {
                    // we do not want this to be an array, otherwise we would have to add additional functionality to the input group component.
                    $currentCategorizedRoofTypesForMe[$cat][] = $currentRoofTypeForMe;
                }
            }
        }

        return view('cooperation.tool.roof-insulation.index', compact(
            'building', 'features', 'primaryRoofTypes', 'secondaryRoofTypes', 'typeIds',
            'buildingFeaturesForMe', 'currentRoofTypes', 'roofTileStatuses', 'roofInsulation', 'currentRoofTypesForMe',
            'heatings', 'measureApplications', 'currentCategorizedRoofTypes', 'currentCategorizedRoofTypesForMe'));
    }

    public function calculate(Request $request)
    {
        /** @var Building $building */
        $building = HoomdossierSession::getBuilding(true);

        $result = \App\Calculations\RoofInsulation::calculate($building,
            HoomdossierSession::getInputSource(true), $building->user->energyHabit, $request->all());

        return response()->json($result);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(RoofInsulationFormRequest $request)
    {
        $building = HoomdossierSession::getBuilding(true);
        $inputSource = HoomdossierSession::getInputSource(true);
        $user = $building->user;

        ConsiderableService::save($this->step, $user, $inputSource,
            $request->validated()['considerables'][$this->step->id]);

        $stepComments = $request->input('step_comments');
        StepCommentService::save($building, $inputSource, $this->step, $stepComments['comment']);

        $dirtyAttributes = json_decode($request->input('dirty_attributes'), true);
        $dirtyNames = array_keys($dirtyAttributes);
        $updatedMeasureIds = [];

        $dirtyPitched = Str::arrStartsWith($dirtyNames, 'building_roof_types[pitched]');
        $dirtyFlat = Str::arrStartsWith($dirtyNames, 'building_roof_types[flat]');

        // We update everything if the primary roof is changed, otherwise we will update the relevant measures for the
        // changed roof category
        if (Str::arrStartsWith($dirtyNames, 'building_features')
            || Str::arrStartsWith($dirtyNames, 'building_roof_type_ids') || ($dirtyFlat && $dirtyPitched)
        ) {
            $updatedMeasureIds = MeasureApplication::findByShorts([
                'roof-insulation-pitched-inside', 'roof-insulation-pitched-replace-tiles',
                'roof-insulation-flat-current', 'roof-insulation-flat-replace-current',
                'replace-tiles', 'replace-roof-insulation', 'replace-zinc-pitched',
                'replace-zinc-flat',
            ])->pluck('id')->toArray();
        } else {
            if ($dirtyFlat) {
                $updatedMeasureIds = MeasureApplication::findByShorts([
                    'roof-insulation-flat-current', 'roof-insulation-flat-replace-current', 'replace-roof-insulation',
                    'replace-zinc-flat',
                ])->pluck('id')->toArray();
            } elseif ($dirtyPitched) {
                $updatedMeasureIds = MeasureApplication::findByShorts([
                    'roof-insulation-pitched-inside', 'roof-insulation-pitched-replace-tiles',
                    'replace-tiles', 'replace-zinc-pitched',
                ])->pluck('id')->toArray();
            }
        }

        $values = $request->only('considerables', 'building_roof_type_ids', 'building_features',
            'building_roof_types', 'step_comments');
        $values['updated_measure_ids'] = $updatedMeasureIds;

        (new RoofInsulationHelper($user, $inputSource))
            ->setValues($values)
            ->saveValues()
            ->createAdvices();

        return $this->completeStore($this->step, $building, $inputSource);
    }
}
