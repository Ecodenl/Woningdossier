<?php

namespace App\Http\Controllers\Cooperation\Tool\GeneralData;

use App\Events\StepDataHasBeenChanged;
use App\Helpers\Hoomdossier;
use App\Helpers\HoomdossierSession;
use App\Helpers\StepHelper;
use App\Http\Requests\Cooperation\Tool\GeneralData\UsageFormRequest;
use App\Models\BuildingHeating;
use App\Models\ComfortLevelTapWater;
use App\Http\Controllers\Controller;
use App\Models\InputSource;
use App\Models\Step;
use App\Models\UserEnergyHabit;
use App\Services\StepCommentService;

class UsageController extends Controller
{
    public function index()
    {
        $building = HoomdossierSession::getBuilding(true);
        $buildingOwner = $building->user;

        $comfortLevelsTapWater = ComfortLevelTapWater::all();
        $userEnergyHabitsForMe = $buildingOwner->energyHabit()->forMe()->get();
        $buildingHeatings = BuildingHeating::all();

        $energyHabitsOrderedOnInputSourceCredibility = Hoomdossier::orderRelationShipOnInputSourceCredibility($buildingOwner->energyHabit())->get();

        $commentsByStep = StepHelper::getAllCommentsByStep($building);
        return view('cooperation.tool.general-data.usage.index', compact(
            'building', 'buildingOwner', 'userEnergyHabitsForMe', 'commentsByStep', 'comfortLevelsTapWater',
            'buildingHeatings', 'energyHabitsOrderedOnInputSourceCredibility'
        ));
    }

    public function store(UsageFormRequest $request)
    {
        $building = HoomdossierSession::getBuilding(true);
        $buildingOwner = $building->user;
        $inputSource = HoomdossierSession::getInputSource(true);
        $step = Step::findByShort('usage');

        $buildingOwner->energyHabit()->updateOrCreate(['input_source_id' => $inputSource->id], $request->input('user_energy_habits'));
        StepCommentService::save($building, $inputSource, $step, $request->input('step_comments.comment'));

        StepHelper::complete($step, $building, $inputSource);
        StepDataHasBeenChanged::dispatch($step, $building, Hoomdossier::user());

        $nextStep = StepHelper::getNextStep($building, $inputSource, $step);
        $url = $nextStep['url'];

        if (!empty($nextStep['tab_id'])) {
            $url .= '#' . $nextStep['tab_id'];
        }

        return redirect($url);
    }
}
