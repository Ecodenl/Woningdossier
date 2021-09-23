<?php

namespace App\Http\Controllers\Cooperation\Tool\GeneralData;

use App\Events\StepDataHasBeenChanged;
use App\Helpers\Hoomdossier;
use App\Helpers\HoomdossierSession;
use App\Helpers\StepHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cooperation\Tool\GeneralData\CurrentStateFormRequest;
use App\Models\BuildingHeatingApplication;
use App\Models\Element;
use App\Models\ElementValue;
use App\Models\Interest;
use App\Models\Service;
use App\Models\ServiceValue;
use App\Models\Step;
use App\Models\UserInterest;
use App\Services\StepCommentService;
use App\Services\UserInterestService;
use Illuminate\Support\Arr;

class CurrentStateController extends Controller
{
    public function index()
    {
        // Route is disabled. Die if they somehow still manage to get here
        die();

        $building = HoomdossierSession::getBuilding(true);
        $buildingOwner = $building->user;
        $userInterestsForMe = UserInterest::forMe()->get();
        $myBuildingFeatures = $building->buildingFeatures()->forMe()->get();

        $elements = Element::whereIn('short', [
            'sleeping-rooms-windows', 'living-rooms-windows', 'crack-sealing',
            'wall-insulation', 'floor-insulation', 'roof-insulation',
        ])->orderBy('order')->with(['values' => function ($query) {
            $query->orderBy('order');
        }])->get();

        $services = Service::orderBy('order')
            ->with(['values' => function ($query) {
                $query->orderBy('order');
            }])->get();

        $buildingHeatingApplications = BuildingHeatingApplication::orderBy('order')->get();

        $commentsByStep = StepHelper::getAllCommentsByStep($building);

        return view('cooperation.tool.general-data.current-state.index', compact(
            'building', 'buildingOwner', 'elements', 'services', 'userInterestsForMe', 'services',
            'buildingHeatingApplications', 'myBuildingFeatures', 'commentsByStep'
        ));
    }

    public function store(CurrentStateFormRequest $request)
    {
        // Route is disabled. Die if they somehow still manage to get here
        die();

        $building = HoomdossierSession::getBuilding(true);
        $buildingOwner = $building->user;
        $inputSource = HoomdossierSession::getInputSource(true);
        $step = Step::findByShort('current-state');


        // save the building elements
        $elements = $request->get('elements', []);
        foreach ($elements as $elementShort => $elementData) {
            $building->buildingElements()->updateOrCreate(
                [
                    'element_id' => $elementData['element_id'],
                    'input_source_id' => $inputSource->id,
                ],
                [
                    'element_value_id' => $elementData['element_value_id'],
                ]
            );
        }

        // save the building services
        $services = $request->get('services', []);
        foreach ($services as $serviceData) {
            $building->buildingServices()->updateOrCreate(
                [
                    'service_id' => $serviceData['service_id'],
                    'input_source_id' => $inputSource->id,
                ],
                Arr::except($serviceData, 'service_id')
            );
        }

        // save buildign features, pv panels and the comments
        $building->pvPanels()->updateOrCreate(['input_source_id' => $inputSource->id], $request->input('building_pv_panels'));
        $building->buildingFeatures()->updateOrCreate(['input_source_id' => $inputSource->id], $request->input('building_features'));
        foreach ($request->input('step_comments.comment') as $short => $comment) {
            StepCommentService::save($building, $inputSource, $step, $comment, $short);
        }

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
