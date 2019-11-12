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
use App\Services\UserInterestService;
use function Couchbase\defaultDecoder;
use Illuminate\Support\Arr;

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
        $step = Step::findByShort('interest');

        $userInterests = $request->input('user_interests');

        foreach ($userInterests as $userInterest) {
            UserInterestService::save($buildingOwner, $inputSource, $userInterest['interested_in_type'], $userInterest['interested_in_id'], $userInterest['interest_id']);
        }

        $buildingOwner->motivations()->delete();
        foreach ($request->input('user_motivations.id') as $order => $moviationId)
            $buildingOwner->motivations()->create([
                'motivation_id' => $moviationId,
                'order' => $order
            ]);
        $buildingOwner->energyHabit()->updateOrCreate([], $request->input('user_energy_habits'));

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
