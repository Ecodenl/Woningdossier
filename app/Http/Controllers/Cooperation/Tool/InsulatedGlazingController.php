<?php

namespace App\Http\Controllers\Cooperation\Tool;

use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use App\Calculations\InsulatedGlazing;
use App\Events\UserToolDataChanged;
use App\Helpers\Cooperation\Tool\InsulatedGlazingHelper;
use App\Helpers\Hoomdossier;
use App\Helpers\HoomdossierSession;
use App\Helpers\Str;
use App\Http\Requests\Cooperation\Tool\InsulatedGlazingFormRequest;
use App\Models\Building;
use App\Models\BuildingElement;
use App\Models\BuildingHeating;
use App\Models\BuildingInsulatedGlazing;
use App\Models\Element;
use App\Models\InsulatingGlazing;
use App\Models\MeasureApplication;
use App\Models\PaintworkStatus;
use App\Models\Step;
use App\Models\ToolQuestion;
use App\Models\WoodRotStatus;
use App\Services\ConsiderableService;
use App\Services\LegacyService;
use App\Services\StepCommentService;
use App\Services\ToolQuestionService;
use Illuminate\Http\Request;
use Illuminate\Support\Str as SupportStr;

class InsulatedGlazingController extends ToolController
{
    /**
     * Display a listing of the resources.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(LegacyService $legacyService): View
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

        $buildingInsulatedGlazings = [];
        $buildingInsulatedGlazingsForMe = [];

        $buildingFeaturesForMe = $building->buildingFeatures()->forMe()->get();

        $measureApplicationShorts = [
            'hrpp-glass-only',
            'hrpp-glass-frames',
            'hr3p-frames',
            'glass-in-lead',
        ];

        foreach ($measureApplicationShorts as $measureApplicationShort) {
            $measureApplication = MeasureApplication::where('short', $measureApplicationShort)->first();

            if ($measureApplication instanceof MeasureApplication) {
                // get current situation
                $currentInsulatedGlazing = $building->currentInsulatedGlazing()
                    ->forInputSource($this->masterInputSource)
                    ->where('measure_application_id', $measureApplication->id)
                    ->first();
                $currentInsulatedGlazingInputs = BuildingInsulatedGlazing::where('measure_application_id', $measureApplication->id)->forMe()->get();

                if (!$currentInsulatedGlazingInputs->isEmpty()) {
                    $buildingInsulatedGlazingsForMe[$measureApplication->id] = $currentInsulatedGlazingInputs;
                }
                if ($currentInsulatedGlazing instanceof BuildingInsulatedGlazing) {
                    $buildingInsulatedGlazings[$measureApplication->id] = $currentInsulatedGlazing;
                }


                $measureApplications[] = $measureApplication;
            }
        }

        $myBuildingElements = BuildingElement::forMe()->get();

        $measureRelatedAnswers = $legacyService->user($building->user)
            ->inputSource(HoomdossierSession::getInputSource(true))
            ->getMeasureRelatedAnswers(Step::findByShort('insulated-glazing'));

        return view('cooperation.tool.insulated-glazing.index', compact(
            'building', 'myBuildingElements', 'buildingOwner',
            'heatings', 'measureApplications', 'insulatedGlazings', 'buildingInsulatedGlazings',
            'crackSealing', 'frames', 'woodElements', 'buildingFeaturesForMe',
            'paintworkStatuses', 'woodRotStatuses', 'buildingInsulatedGlazingsForMe', 'buildingPaintworkStatusesOrderedOnInputSourceCredibility',
            'measureRelatedAnswers'
        ));
    }

    public function calculate(Request $request): JsonResponse
    {
        $building = HoomdossierSession::getBuilding(true);

        $result = InsulatedGlazing::calculate(
            $building,
            $this->masterInputSource,
            $building->user->energyHabit()->forInputSource($this->masterInputSource)->first(),
            $request->all()
        );

        return response()->json($result);
    }

    /**
     * Store the incoming request and redirect to the next step.
     */
    public function store(InsulatedGlazingFormRequest $request, LegacyService $legacyService, ToolQuestionService $toolQuestionService): RedirectResponse
    {
        $building = HoomdossierSession::getBuilding(true);
        $inputSource = HoomdossierSession::getInputSource(true);
        $user = $building->user;

        $considerables = $request->validated()['considerables'];
        foreach ($considerables as $considerableId => $considerableData) {
            // so we can determine the highest interest level later on.
            ConsiderableService::save(MeasureApplication::findOrFail($considerableId), $user, $inputSource, $considerableData);
        }

        $stepComments = $request->input('step_comments');
        StepCommentService::save($building, $inputSource, $this->step, $stepComments['comment']);

        $toolQuestionService->building($building)->currentInputSource($inputSource);
        $measureRelatedShorts = $legacyService->getToolQuestionShorts(Step::findByShort('insulated-glazing'));
        foreach ($measureRelatedShorts as $measureId => $tqShorts) {
            if ($considerables[$measureId]['is_considering']) {
                foreach ($tqShorts as $tqShort) {
                    // Subsidy question might have been removed and thus not saveable.
                    if (array_key_exists($tqShort, $request->validated())) {
                        $tq = ToolQuestion::findByShort($tqShort);
                        $toolQuestionService->toolQuestion($tq)->save($request->validated()[$tqShort]);
                    }
                }
            }
        }

        $dirtyAttributes = json_decode($request->input('dirty_attributes'), true);
        if (!empty($dirtyAttributes)) {
            UserToolDataChanged::dispatch($user);
        }

        // Time to check for building_insulated_glazings in the dirtyAttributes
        // We don't care for the values attached, if they're here, it means the user has messed with them
        $dirtyNames = array_keys($dirtyAttributes);
        $updatedMeasureIds = [];
        // Check if any of the values have the name we check for
        if (Str::arrStartsWith($dirtyNames, 'building_insulated_glazings', true)) {
            // There are some, let's fetch the measure IDs
            foreach ($dirtyNames as $dirtyName) {
                if (SupportStr::startsWith($dirtyName, 'building_insulated_glazings')) {
                    // Format always has the ID as second attr
                    $id = explode('.', Str::htmlArrToDot($dirtyName))[1] ?? null;
                    if (!is_null($id) && !in_array($id, $updatedMeasureIds)) {
                        // Add ID
                        $updatedMeasureIds[] = $id;
                    }
                }
            }
        }

        // Add the paint measure if any of the building elements were changed
        if (Str::arrStartsWith($dirtyNames, 'building_elements', true)) {
            if (($paintMeasure = MeasureApplication::findByShort('paint-wood-elements')) instanceof MeasureApplication) {
                $updatedMeasureIds[] = $paintMeasure->id;
            }
        }

        $values = $request->only('considerables', 'building_insulated_glazings', 'building_features',
            'building_elements', 'building_paintwork_statuses');
        $values['updated_measure_ids'] = $updatedMeasureIds;

        (new InsulatedGlazingHelper($user, $inputSource))
            ->setValues($values)
            ->saveValues()
            ->createAdvices();

        return $this->completeStore($this->step, $building, $inputSource);
    }
}
