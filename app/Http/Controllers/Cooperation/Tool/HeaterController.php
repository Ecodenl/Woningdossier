<?php

namespace App\Http\Controllers\Cooperation\Tool;

use App\Calculations\Heater;
use App\Helpers\Cooperation\Tool\HeaterHelper;
use App\Helpers\Hoomdossier;
use App\Helpers\HoomdossierSession;
use App\Http\Requests\Cooperation\Tool\HeaterFormRequest;
use App\Models\ComfortLevelTapWater;
use App\Models\MeasureApplication;
use App\Models\PvPanelOrientation;
use App\Services\ConsiderableService;
use App\Services\StepCommentService;
use Illuminate\Http\Request;

class HeaterController extends ToolController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        throw new \Exception("Heater index used! Referer: " . $request->header('referer'));

        $typeIds = [3];

        $building = HoomdossierSession::getBuilding(true);
        $buildingOwner = $building->user;

        $comfortLevels = ComfortLevelTapWater::orderBy('order')->get();
        $collectorOrientations = PvPanelOrientation::orderBy('order')->get();

        $energyHabitsOrderedOnInputSourceCredibility = Hoomdossier::orderRelationShipOnInputSourceCredibility(
            $buildingOwner->energyHabit()
        )->get();

        $heatersOrderedOnInputSourceCredibility = Hoomdossier::orderRelationShipOnInputSourceCredibility(
            $building->heater()
        )->get();

        return view('cooperation.tool.heater.index', compact('building', 'buildingOwner',
            'collectorOrientations', 'typeIds', 'energyHabitsOrderedOnInputSourceCredibility', 'comfortLevels',
            'heatersOrderedOnInputSourceCredibility'
        ));
    }

    public function calculate(Request $request)
    {
        throw new \Exception("Heater calculate used! Referer: " . $request->header('referer'));

        $building = HoomdossierSession::getBuilding(true);
        $user = $building->user;

        $result = Heater::calculate($building, $user->energyHabit()->forInputSource($this->masterInputSource)->first(), $request->all());

        return response()->json($result);
    }

    /**
     * Store or update the existing record.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(HeaterFormRequest $request)
    {
        throw new \Exception("Heater store used! Referer: " . $request->header('referer'));

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
                'heater-place-replace',
            ])
                ->pluck('id')
                ->toArray();
        }

        $values = $request->only('building_heaters', 'user_energy_habits', 'considerables');
        $values['updated_measure_ids'] = $updatedMeasureIds;

        (new HeaterHelper($user, $inputSource))
            ->setValues($values)
            ->saveValues()
            ->createAdvices();

        return $this->completeStore($this->step, $building, $inputSource);
    }
}
