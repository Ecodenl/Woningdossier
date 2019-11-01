<?php

namespace App\Http\Controllers\Cooperation\Tool\GeneralData;

use App\Helpers\HoomdossierSession;
use App\Models\Element;
use App\Models\Interest;
use App\Models\Motivation;
use App\Models\Service;
use App\Http\Controllers\Controller;
use App\Models\UserInterest;

class InterestController extends Controller
{
    public function index()
    {
        $building = HoomdossierSession::getBuilding(true);
        $buildingOwner = $building->user;

//        if ($buildingOwner->motivations()->count() > 0) {
            $motivations = Motivation::leftJoin('user_motivations', 'motivations.id', '=', 'user_motivations.motivation_id')
                ->select('motivations.*')
                ->where('user_motivations.user_id', $buildingOwner->id)
                ->orderBy('user_motivations.order')->get();
//        } else {
//            $motivations = Motivation::orderBy('order')->get();
//        }

        $userMotivations = $buildingOwner->motivations()->orderBy('order')->get();
//        dd($userMotivations);
        $userEnergyHabitsForMe = $buildingOwner->energyHabit()->forMe()->get();
//        $elements = Element::whereIn('short', [
//            'sleeping-rooms-windows', 'living-rooms-windows',
//            'wall-insulation', 'floor-insulation', 'roof-insulation',
//        ])->orderBy('order')->get();

//        $elementInterests = $buildingOwner
//            ->interests()
//            ->where('interested_in_type', 'element')
//            ->whereIn('interested_in_id', $elements->pluck('id')->toArray())
//            ->get();
//        dd($elementInterests);


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
