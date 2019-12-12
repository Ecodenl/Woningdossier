<?php

namespace App\Http\Controllers\Cooperation\Tool\GeneralData;

use App\Events\StepDataHasBeenChanged;
use App\Helpers\Hoomdossier;
use App\Helpers\HoomdossierSession;
use App\Helpers\NumberFormatter;
use App\Helpers\StepHelper;
use App\Http\Requests\Cooperation\Tool\GeneralData\InterestFormRequest;
use App\Models\Cooperation;
use App\Models\Interest;
use App\Models\Motivation;
use App\Models\Service;
use App\Http\Controllers\Controller;
use App\Models\Step;
use App\Services\StepCommentService;
use App\Services\UserInterestService;

class InterestController extends Controller
{
    public function index(Cooperation $cooperation)
    {
        $building = HoomdossierSession::getBuilding(true);
        $buildingOwner = $building->user->load('stepInterests');
        $steps = $cooperation->getActiveOrderedSteps();

        // we wont show the general data cause we cant give our interest for that
        $steps = $steps->keyBy('short')->forget('general-data');
        // used in the forloopt
        $stepCount = $steps->count();


        // we have to display the services on the left side, and elements on right side
        // collect the steps in a service / element order
        $servicesStepShorts = array_flip([
            'high-efficiency-boiler', 'heat-pump', 'solar-panels', 'heater', 'ventilation'
        ]);
        $elementsStepShorts = array_flip([
            'insulated-glazing', 'wall-insulation', 'floor-insulation', 'roof-insulation'
        ]);

        // filter the shorts
        // when the step isnt found in the collection
        // well then its turned of by the cooperation, so do not return it
        $servicesStepShorts = array_filter($servicesStepShorts, function ($stepShort) use ($steps) {
            if (is_null($steps->where('short', $stepShort)->first())) {
                return false;
            }
            return true;
        }, ARRAY_FILTER_USE_KEY);

        $elementsStepShorts = array_filter($elementsStepShorts, function ($stepShort) use ($steps) {
            if (is_null($steps->where('short', $stepShort)->first())) {
                return false;
            }
            return true;
        }, ARRAY_FILTER_USE_KEY);


        $motivations = Motivation::orderBy('order')->get();

        $userMotivations = $buildingOwner->motivations()->orderBy('order')->get();
        $userEnergyHabitsForMe = $buildingOwner->energyHabit()->forMe()->get();

        $services = Service::orderBy('order')->get();

        $interests = Interest::orderBy('order')->get();

        // because the $steps is loaded with view composers and will be overwriten
        $filteredSteps = $steps;
        return view('cooperation.tool.general-data.interest.index', compact(
            'interests', 'services', 'elements', 'motivations', 'userMotivations', 'userEnergyHabitsForMe',
            'buildingOwner', 'stepCount', 'servicesStepShorts', 'elementsStepShorts', 'filteredSteps'
        ));
    }

    public function store(InterestFormRequest $request)
    {
        $building = HoomdossierSession::getBuilding(true);
        $buildingOwner = $building->user;
        $inputSource = HoomdossierSession::getInputSource(true);
        $step = Step::findByShort('interest');

        $userInterests = $request->input('user_interests');

        foreach ($userInterests as $interestInId => $userInterest) {
            UserInterestService::save($buildingOwner, $inputSource, Step::class, $interestInId, $userInterest['interest_id']);
        }

        $buildingOwner->motivations()->delete();

        $userMotivations = $request->input('user_motivations.id');
        if (!empty($userMotivations)) {
            foreach ($request->input('user_motivations.id') as $order => $moviationId)
                $buildingOwner->motivations()->create([
                    'motivation_id' => $moviationId,
                    'order' => $order
                ]);
        }
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
