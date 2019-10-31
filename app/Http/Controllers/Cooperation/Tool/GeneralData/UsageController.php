<?php

namespace App\Http\Controllers\Cooperation\Tool\GeneralData;

use App\Helpers\HoomdossierSession;
use App\Helpers\StepHelper;
use App\Models\BuildingHeating;
use App\Models\ComfortLevelTapWater;
use App\Http\Controllers\Controller;

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
}
