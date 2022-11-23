<?php

namespace App\Services\Scans\ExpertScan;

use App\Helpers\Cooperation\Tool\WallInsulationHelper;
use App\Helpers\Hoomdossier;
use App\Helpers\HoomdossierSession;
use App\Models\Building;
use App\Models\BuildingElement;
use App\Models\BuildingFeature;
use App\Models\FacadeDamagedPaintwork;
use App\Models\FacadePlasteredSurface;
use App\Models\FacadeSurface;
use App\Models\InputSource;
use App\Models\MeasureApplication;
use App\Models\Step;
use App\Scopes\GetValueScope;
use App\Services\ConsiderableService;
use App\Services\Scans\ExpertScan\ScanableStep;
use App\Services\StepCommentService;
use Illuminate\Http\Request;

class WallInsulation extends ScanableStep {

    public function data(): array
    {
        $typeIds = [3];

        $building = $this->building;

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

        return compact(
            'building', 'facadeInsulation', 'buildingFeaturesOrderedOnCredibility',
            'surfaces', 'buildingFeature', 'typeIds',
            'facadePlasteredSurfaces', 'facadeDamages', 'buildingFeaturesForMe',
            'buildingElements', 'buildingFeaturesRelationShip'
        );
    }

    public function save(Request $request)
    {

        $building = $this->building;
        $inputSource = $this->inputSource;
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
    }

    public function calculate()
    {
        $building = $this->building;
        $user = $building->user;
        $userEnergyHabit = $user->energyHabit()->forInputSource($this->masterInputSource)->first();

        $result = \App\Calculations\WallInsulation::calculate($building, $this->masterInputSource, $userEnergyHabit, $this->request->toArray());
    }
}