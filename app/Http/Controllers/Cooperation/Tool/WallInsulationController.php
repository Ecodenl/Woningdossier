<?php

namespace App\Http\Controllers\Cooperation\Tool;

use App\Calculations\WallInsulation;
use App\Helpers\Cooperation\Tool\WallInsulationHelper;
use App\Helpers\Hoomdossier;
use App\Helpers\HoomdossierSession;
use App\Http\Requests\Cooperation\Tool\WallInsulationRequest;
use App\Models\Building;
use App\Models\BuildingElement;
use App\Models\BuildingFeature;
use App\Models\FacadeDamagedPaintwork;
use App\Models\FacadePlasteredSurface;
use App\Models\FacadeSurface;
use App\Models\MeasureApplication;
use App\Scopes\GetValueScope;
use App\Services\ConsiderableService;
use App\Services\StepCommentService;

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
        $buildingFeature = $building->buildingFeatures;
        $buildingElements = $facadeInsulation->element;

        $buildingFeaturesRelationShip = $building->buildingFeatures();

        $buildingFeaturesOrderedOnCredibility = Hoomdossier::orderRelationShipOnInputSourceCredibility($buildingFeaturesRelationShip)->get();

        $buildingFeaturesForMe = BuildingFeature::withoutGlobalScope(GetValueScope::class)->forMe()->get();

        /** @var BuildingElement $houseInsulation */
        $surfaces = FacadeSurface::orderBy('order')->get();
        $facadePlasteredSurfaces = FacadePlasteredSurface::orderBy('order')->get();
        $facadeDamages = FacadeDamagedPaintwork::orderBy('order')->get();

        return view('cooperation.tool.wall-insulation.index', compact(
             'building', 'facadeInsulation', 'buildingFeaturesOrderedOnCredibility',
            'surfaces', 'buildingFeature', 'typeIds',
            'facadePlasteredSurfaces', 'facadeDamages', 'buildingFeaturesForMe',
            'buildingElements', 'buildingFeaturesRelationShip'
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

        ConsiderableService::save($this->step, $user, $inputSource, $request->validated()['considerables'][$this->step->id]);

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

        $values = $request->validated();
        $values['updated_measure_ids'] = $updatedMeasureIds;

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
        $userEnergyHabit = $user->energyHabit;

        $result = WallInsulation::calculate($building, HoomdossierSession::getInputSource(true), $userEnergyHabit, $request->toArray());

        return response()->json($result);
    }
}
