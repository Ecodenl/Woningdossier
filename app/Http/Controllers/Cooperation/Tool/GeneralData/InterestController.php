<?php

namespace App\Http\Controllers\Cooperation\Tool\GeneralData;

use App\Helpers\HoomdossierSession;
use App\Models\Cooperation;
use App\Models\Element;
use App\Models\Interest;
use App\Models\Motivation;
use App\Models\Service;
use App\Http\Controllers\Controller;
use App\Models\Step;
use App\Models\UserInterest;

class InterestController extends Controller
{
    public function index()
    {
        $building = HoomdossierSession::getBuilding(true);
        $buildingOwner = $building->user->load('stepInterests');

        $motivations = Motivation::orderBy('order')->get();


        $userMotivations = $buildingOwner->motivations()->orderBy('order')->get();
        $userEnergyHabitsForMe = $buildingOwner->energyHabit()->forMe()->get();

        $services = Service::orderBy('order')->get();

        $interests = Interest::orderBy('order')->get();

        return view('cooperation.tool.general-data.interest.index', compact(
            'interests', 'services', 'elements', 'motivations', 'userMotivations', 'userEnergyHabitsForMe',
            'buildingOwner'
        ));
    }

    public function store()
    {

    }
}
