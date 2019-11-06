<?php

namespace App\Http\Controllers\Cooperation\Tool\GeneralData;

use App\Events\StepDataHasBeenChanged;
use App\Helpers\Hoomdossier;
use App\Helpers\HoomdossierSession;
use App\Helpers\StepHelper;
use App\Http\Requests\Cooperation\Tool\GeneralData\InterestFormRequest;
use App\Models\Cooperation;
use App\Models\Element;
use App\Models\Interest;
use App\Models\Motivation;
use App\Models\Service;
use App\Http\Controllers\Controller;
use App\Models\Step;
use App\Models\UserInterest;
use App\Services\StepCommentService;

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
    public function store(InterestFormRequest $request)
    {
        $building = HoomdossierSession::getBuilding(true);
        $buildingOwner = $building->user;
        $inputSource = HoomdossierSession::getInputSource(true);
        $step = Step::findByShort('usage');

        $buildingOwner->energyHabit()->updateOrCreate([], $request->input('user_energy_habits'));
        StepCommentService::save($building, $inputSource, $step, $request->input('step_comments.comment'));

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
