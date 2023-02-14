<?php

namespace App\Http\Controllers\Cooperation\Tool;

use App\Calculations\WallInsulation;
use App\Helpers\Arr;
use App\Helpers\Cooperation\Tool\WallInsulationHelper;
use App\Helpers\Hoomdossier;
use App\Helpers\HoomdossierSession;
use App\Helpers\KeyFigures\WallInsulation\Temperature;
use App\Http\Requests\Cooperation\Tool\WallInsulationRequest;
use App\Models\Building;
use App\Models\BuildingElement;
use App\Models\BuildingFeature;
use App\Models\FacadeDamagedPaintwork;
use App\Models\FacadePlasteredSurface;
use App\Models\FacadeSurface;
use App\Models\MeasureApplication;
use App\Models\Step;
use App\Scopes\GetValueScope;
use App\Services\ConsiderableService;
use App\Services\Models\UserCostService;
use App\Services\StepCommentService;
use Illuminate\Support\Facades\Log;

class WallInsulationController extends ToolController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $typeIds = [3];

        /** @var Building $building */
        $building = HoomdossierSession::getBuilding(true);

        $facadeInsulation = $building->getBuildingElement('wall-insulation', $this->masterInputSource);
        $buildingFeature = $building->buildingFeatures()->forInputSource($this->masterInputSource)->first();
        $buildingElements = $facadeInsulation->element;

        $buildingFeaturesRelationShip = $building->buildingFeatures();

        $buildingFeaturesOrderedOnCredibility = Hoomdossier::orderRelationShipOnInputSourceCredibility($buildingFeaturesRelationShip)->get();

        $buildingFeaturesForMe = BuildingFeature::withoutGlobalScope(GetValueScope::class)->forMe()->get();

        /** @var BuildingElement $houseInsulation */
        $surfaces = FacadeSurface::orderBy('order')->get();
        $facadePlasteredSurfaces = FacadePlasteredSurface::orderBy('order')->get();
        $facadeDamages = FacadeDamagedPaintwork::orderBy('order')->get();

        $userCosts = UserCostService::init($building->user, HoomdossierSession::getInputSource(true))
            ->forAdvisable(Step::findByShort('wall-insulation'))
            ->getAnswers(true);

        return view('cooperation.tool.wall-insulation.index', compact(
             'building', 'facadeInsulation', 'buildingFeaturesOrderedOnCredibility',
            'surfaces', 'buildingFeature', 'typeIds',
            'facadePlasteredSurfaces', 'facadeDamages', 'buildingFeaturesForMe',
            'buildingElements', 'buildingFeaturesRelationShip', 'userCosts'
        ));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Cooperation\Tool\WallInsulationRequest  $request
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(WallInsulationRequest $request)
    {
        $building = HoomdossierSession::getBuilding(true);
        $inputSource = HoomdossierSession::getInputSource(true);
        $user = $building->user;

        $considerables = $request->validated()['considerables'];
        ConsiderableService::save($this->step, $user, $inputSource, $considerables[$this->step->id]);

        $stepComments = $request->input('step_comments');
        StepCommentService::save($building, $inputSource, $this->step, $stepComments['comment']);

        $dirtyAttributes = json_decode($request->input('dirty_attributes'), true);
        $updatedMeasureIds = [];
        // If anything's dirty, all measures must be recalculated (we can't really check specifics here)
        if (! empty($dirtyAttributes)) {
            $updatedMeasureIds = MeasureApplication::findByShorts([
                'cavity-wall-insulation', 'facade-wall-insulation', 'wall-insulation-research',
                'paint-wall', 'repair-joint', 'clean-brickwork', 'impregnate-wall',
            ])
                ->pluck('id')
                ->toArray();
        }

        $cavityWallAdvice = [
            1 => Temperature::WALL_INSULATION_JOINTS,
            2 => Temperature::WALL_INSULATION_FACADE,
            0 => Temperature::WALL_INSULATION_RESEARCH,
        ];

        $advice = $cavityWallAdvice[$request->validated()['building_features']['cavity_wall']] ?? Temperature::WALL_INSULATION_JOINTS;

        $userCosts = $request->validated()['user_costs'];
        $userCostService = UserCostService::init($user, $inputSource);
        $userCostValues = [];
        if (Arr::first($considerables)['is_considering']) {
            foreach ($userCosts as $measureShort => $costData) {
                // Only save for connected type
                if ($measureShort === $advice) {
                    $measureApplication = MeasureApplication::findByShort($measureShort);
                    $userCostService->forAdvisable($measureApplication)->sync($costData);
                    $userCostValues[$measureShort] = $costData;
                }
            }
        }

        $values = $request->validated();
        // As of right now, values are not dynamically updated. Therefore, if the answer for facade_plastered_painted
        // is set to "no", we will nullify related questions.
        $answer = Arr::get($values, 'building_features.facade_plastered_painted');
        if ($answer == 2) {
            Arr::set($values, 'building_features.facade_damaged_paintwork_id', null);
            Arr::set($values, 'building_features.facade_plastered_surface_id', null);
        }
        $values['updated_measure_ids'] = $updatedMeasureIds;
        $values['user_costs'] = $userCostValues;

        (new WallInsulationHelper($user, $inputSource))
            ->setValues($values)
            ->saveValues()
            ->createAdvices();

        return $this->completeStore($this->step, $building, $inputSource);
    }

    public function calculate(WallInsulationRequest $request)
    {
        $building = HoomdossierSession::getBuilding(true);
        $user = $building->user;
        $userEnergyHabit = $user->energyHabit()->forInputSource($this->masterInputSource)->first();

        $result = WallInsulation::calculate($building, $this->masterInputSource, $userEnergyHabit, $request->toArray());

        return response()->json($result);
    }
}
