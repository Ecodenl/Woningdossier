<?php

namespace App\Http\Controllers\Cooperation\Tool;

use App\Calculations\InsulatedGlazing;
use App\Helpers\Cooperation\Tool\InsulatedGlazingHelper;
use App\Helpers\Hoomdossier;
use App\Helpers\HoomdossierSession;
use App\Http\Requests\Cooperation\Tool\InsulatedGlazingFormRequest;
use App\Models\Building;
use App\Models\BuildingElement;
use App\Models\BuildingHeating;
use App\Models\BuildingInsulatedGlazing;
use App\Models\Element;
use App\Models\InsulatingGlazing;
use App\Models\MeasureApplication;
use App\Models\PaintworkStatus;
use App\Models\WoodRotStatus;
use App\Services\ConsiderableService;
use App\Services\StepCommentService;
use Illuminate\Http\Request;

class InsulatedGlazingController extends ToolController
{
    /**
     * Display a listing of the resources.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        /**
         * @var Building
         */
        $building = HoomdossierSession::getBuilding(true);
        $buildingOwner = $building->user;

        $buildingPaintworkStatusesOrderedOnInputSourceCredibility = Hoomdossier::orderRelationShipOnInputSourceCredibility(
            $building->currentPaintworkStatus()
        )->get();

        $insulatedGlazings = InsulatingGlazing::all();
        $crackSealing = Element::where('short', 'crack-sealing')->first();
        $frames = Element::where('short', 'frames')->first();
        $woodElements = Element::where('short', 'wood-elements')->first();
        $heatings = BuildingHeating::where('calculate_value', '<', 5)->get(); // we don't want n.v.t.
        $paintworkStatuses = PaintworkStatus::orderBy('order')->get();
        $woodRotStatuses = WoodRotStatus::orderBy('order')->get();

        $measureApplicationShorts = [
            'hrpp-glass-only',
            'hrpp-glass-frames',
            'hr3p-frames',
            'glass-in-lead',
        ];

        $buildingInsulatedGlazings = [];
        $buildingInsulatedGlazingsForMe = [];

        $buildingFeaturesForMe = $building->buildingFeatures()->forMe()->get();

        foreach ($measureApplicationShorts as $measureApplicationShort) {
            $measureApplication = MeasureApplication::where('short', $measureApplicationShort)->first();

            if ($measureApplication instanceof MeasureApplication) {
                // get current situation
                $currentInsulatedGlazing = $building->currentInsulatedGlazing()->where('measure_application_id', $measureApplication->id)->first();
                $currentInsulatedGlazingInputs = BuildingInsulatedGlazing::where('measure_application_id', $measureApplication->id)->forMe()->get();

                if (! $currentInsulatedGlazingInputs->isEmpty()) {
                    $buildingInsulatedGlazingsForMe[$measureApplication->id] = $currentInsulatedGlazingInputs;
                }
                if ($currentInsulatedGlazing instanceof BuildingInsulatedGlazing) {
                    $buildingInsulatedGlazings[$measureApplication->id] = $currentInsulatedGlazing;
                }


                $measureApplications[] = $measureApplication;
            }
        }

        $myBuildingElements = BuildingElement::forMe()->get();

        return view('cooperation.tool.insulated-glazing.index', compact(
            'building', 'myBuildingElements', 'buildingOwner',
            'heatings', 'measureApplications', 'insulatedGlazings', 'buildingInsulatedGlazings',
            'crackSealing', 'frames', 'woodElements', 'buildingFeaturesForMe',
            'paintworkStatuses', 'woodRotStatuses', 'buildingInsulatedGlazingsForMe', 'buildingPaintworkStatusesOrderedOnInputSourceCredibility'
        ));
    }

    public function calculate(Request $request)
    {
        $building = HoomdossierSession::getBuilding(true);
        $inputSource = HoomdossierSession::getInputSource(true);

        $result = InsulatedGlazing::calculate($building, $inputSource, $building->user->energyHabit, $request->all());

        return response()->json($result);
    }

    /**
     * Store the incoming request and redirect to the next step.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(InsulatedGlazingFormRequest $request)
    {
        $building = HoomdossierSession::getBuilding(true);
        $inputSource = HoomdossierSession::getInputSource(true);
        $user = $building->user;

        foreach ($request->validated()['considerables'] as $considerableId => $considerableData) {
            // so we can determine the highest interest level later on.
            ConsiderableService::save(MeasureApplication::findOrFail($considerableId), $user, $inputSource, $considerableData);
        }

        $stepComments = $request->input('step_comments');
        StepCommentService::save($building, $inputSource, $this->step, $stepComments['comment']);

        (new InsulatedGlazingHelper($user, $inputSource))
            ->setValues($request->only('considerables', 'user_interests', 'building_insulated_glazings', 'building_features', 'building_elements', 'building_paintwork_statuses'))
            ->saveValues()
            ->createAdvices();

        return $this->completeStore($this->step, $building, $inputSource);
    }
}
