<?php

namespace App\Http\Controllers\Cooperation\Tool;

use App\Calculations\Heater;
use App\Events\StepDataHasBeenChanged;
use App\Helpers\Calculation\BankInterestCalculator;
use App\Helpers\Calculator;
use App\Helpers\Hoomdossier;
use App\Helpers\HoomdossierSession;
use App\Helpers\Kengetallen;
use App\Helpers\KeyFigures\Heater\KeyFigures;
use App\Helpers\NumberFormatter;
use App\Helpers\StepHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\HeaterFormRequest;
use App\Models\Building;
use App\Models\BuildingHeater;
use App\Models\ComfortLevelTapWater;
use App\Models\Cooperation;
use App\Models\HeaterComponentCost;
use App\Models\Interest;
use App\Models\KeyFigureConsumptionTapWater;
use App\Models\MeasureApplication;
use App\Models\PvPanelLocationFactor;
use App\Models\PvPanelOrientation;
use App\Models\PvPanelYield;
use App\Models\Step;
use App\Models\UserActionPlanAdvice;
use App\Models\UserEnergyHabit;
use App\Models\UserInterest;
use App\Scopes\GetValueScope;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HeaterController extends Controller
{
    /**
     * @var Step
     */
    protected $step;

    public function __construct(Request $request)
    {
        $slug = str_replace('/tool/', '', $request->getRequestUri());
        $this->step = Step::where('slug', $slug)->first();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $typeIds = [3];

        $building = HoomdossierSession::getBuilding(true);
        $buildingOwner = $building->user;

        $comfortLevels = ComfortLevelTapWater::orderBy('order')->get();
        $collectorOrientations = PvPanelOrientation::orderBy('order')->get();
        /** @var UserEnergyHabit|null $habits */
        $habits = $buildingOwner->energyHabit;
        $userEnergyHabitsForMe = UserEnergyHabit::forMe()->get();
        $currentComfort = null;
        if ($habits instanceof UserEnergyHabit) {
            $currentComfort = $habits->comfortLevelTapWater;
        }
        $currentHeater = $building->heater;
        $currentHeatersForMe = $building->heater()->forMe()->get();

        return view('cooperation.tool.heater.index', compact('building', 'buildingOwner',
            'comfortLevels', 'collectorOrientations', 'typeIds', 'userEnergyHabitsForMe',
            'currentComfort', 'currentHeater', 'habits', 'currentHeatersForMe'
        ));
    }

    public function calculate(Request $request)
    {
        $building = HoomdossierSession::getBuilding(true);
        $user = $building->user;

        $result = Heater::calculate($building, $user->energyHabit, $request->all());

        return response()->json($result);
    }

    /**
     * Store or update the existing record.
     *
     * @param HeaterFormRequest $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(HeaterFormRequest $request)
    {
        $building = HoomdossierSession::getBuilding(true);
        $user = $building->user;
        $buildingId = $building->id;
        $inputSourceId = HoomdossierSession::getInputSource();

        $interests = $request->input('interest', '');
        UserInterest::saveUserInterests($user, $interests);

        // Store the building heater part
        $buildingHeaters = $request->input('building_heaters', '');
        $pvPanelOrientation = isset($buildingHeaters['pv_panel_orientation_id']) ? $buildingHeaters['pv_panel_orientation_id'] : '';
        $angle = isset($buildingHeaters['angle']) ? $buildingHeaters['angle'] : '';
        $comment = $request->get('comment', '');
        $comment = is_null($comment) ? '' : $comment;

        BuildingHeater::withoutGlobalScope(GetValueScope::class)->updateOrCreate(
            [
                'building_id' => $buildingId,
                'input_source_id' => $inputSourceId,
            ],
            [
                'pv_panel_orientation_id' => $pvPanelOrientation,
                'angle' => $angle,
                'comment' => $comment,
            ]
        );

        // Update the habit
        $habits = $request->input('user_energy_habits', '');
        $waterComFortId = isset($habits['water_comfort_id']) ? $habits['water_comfort_id'] : '';

        $user->energyHabit()->withoutGlobalScope(GetValueScope::class)->update(['water_comfort_id' => $waterComFortId]);

        // Save progress
        $this->saveAdvices($request);
        StepHelper::complete($this->step, $building, HoomdossierSession::getInputSource(true));
        StepDataHasBeenChanged::dispatch($this->step, $building, Hoomdossier::user());

        $nextStep = StepHelper::getNextStep($building, HoomdossierSession::getInputSource(true), $this->step);
        $url = $nextStep['url'];

        if (! empty($nextStep['tab_id'])) {
            $url .= '#'.$nextStep['tab_id'];
        }

        return redirect($url);
    }

    protected function saveAdvices(Request $request)
    {
        $building = HoomdossierSession::getBuilding(true);
        $user = $building->user;

        /** @var JsonResponse $results */
        $results = $this->calculate($request);
        $results = $results->getData(true);

        // Remove old results
        UserActionPlanAdvice::forMe()->where('input_source_id', HoomdossierSession::getInputSource())->forStep($this->step)->delete();

        if (isset($results['cost_indication']) && $results['cost_indication'] > 0) {
            $measureApplication = MeasureApplication::where('short', 'heater-place-replace')->first();
            if ($measureApplication instanceof MeasureApplication) {
                $actionPlanAdvice = new UserActionPlanAdvice($results);
                $actionPlanAdvice->costs = $results['cost_indication']; // only outlier
                $actionPlanAdvice->user()->associate($user);
                $actionPlanAdvice->measureApplication()->associate($measureApplication);
                $actionPlanAdvice->step()->associate($this->step);
                $actionPlanAdvice->save();
            }
        }
    }
}
