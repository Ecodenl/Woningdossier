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
use App\Models\Step;

class UsageController extends Controller
{
    public function index()
    {
        $building = HoomdossierSession::getBuilding(true);
        $buildingOwner = $building->user;
        $comfortLevelsTapWater = ComfortLevelTapWater::all();
        $userEnergyHabitsForMe = $buildingOwner->energyHabit()->forMe()->get();
        $buildingHeatings = BuildingHeating::all();


        $commentsByStep = StepHelper::getAllCommentsByStep($buildingOwner);
        return view('cooperation.tool.general-data.usage.index', compact(
            'building', 'buildingOwner', 'userEnergyHabitsForMe', 'commentsByStep', 'comfortLevelsTapWater',
            'buildingHeatings'
        ));
    }

    public function store(UsageFormRequest $request)
    {
        $building = HoomdossierSession::getBuilding(true);
        $buildingOwner = $building->user;
        $inputSource = HoomdossierSession::getInputSource(true);
        $step = Step::findByShort('current-state');

        \DB::enableQueryLog();
        $buildingOwner->energyHabit()->updateOrCreate([], $request->input('user_energy_habits'));
        dd(\DB::getQueryLog());

        StepHelper::complete($step, $building, $inputSource);
        StepDataHasBeenChanged::dispatch($step, $building, Hoomdossier::user());

        $nextStep = StepHelper::getNextStep($building, $inputSource, $step);
        $url = $nextStep['url'];

        if (! empty($nextStep['tab_id'])) {
            $url .= '#'.$nextStep['tab_id'];
        }

        return redirect($url);
    }
}
