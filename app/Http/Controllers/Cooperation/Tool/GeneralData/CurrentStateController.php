<?php

namespace App\Http\Controllers\Cooperation\Tool\GeneralData;

use App\Events\StepDataHasBeenChanged;
use App\Helpers\Hoomdossier;
use App\Helpers\HoomdossierSession;
use App\Helpers\StepHelper;
use App\Http\Requests\Cooperation\Tool\GeneralData\CurrentStateRequest;
use App\Models\BuildingHeatingApplication;
use App\Models\BuildingService;
use App\Models\Cooperation;
use App\Models\Element;
use App\Models\ElementValue;
use App\Models\ExampleBuildingContent;
use App\Models\InputSource;
use App\Models\Service;
use App\Models\ServiceValue;
use App\Models\Step;
use App\Models\UserInterest;
use App\Http\Controllers\Controller;
use App\Services\StepCommentService;

class CurrentStateController extends Controller
{
    public function index()
    {

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

        $commentsByStep = StepHelper::getAllCommentsByStep($buildingOwner);
        return view('cooperation.tool.general-data.current-state.index', compact(
            'building', 'buildingOwner', 'elements', 'services', 'userInterestsForMe', 'services',
            'buildingHeatingApplications', 'myBuildingFeatures', 'commentsByStep'
        ));
    }

    public function store(CurrentStateRequest $request)
    {
        $building = HoomdossierSession::getBuilding(true);
        $inputSource = HoomdossierSession::getInputSource(true);
        $step = Step::findByShort('current-state');


        $elements = $request->get('element', []);
        foreach ($elements as $elementId => $elementValueId) {
            $element = Element::find($elementId);
            if ($element instanceof Element) {
                $building->buildingElements()->updateOrCreate(['element_id' => $element->id], [
                    'element_value_id' => $elementValueId,
                ]);
            }
        }

        // save the services
        $services = $request->get('service', []);
        foreach ($services as $serviceId => $serviceValueId) {
            // get the service based on the service id from the form
            $service = Service::find($serviceId);

            if ($service instanceof Service) {

                $buildingServiceUpdateData = [];
                // check if the current service is a sun panel
                // if so, we will need to put the value / valueId inside the extra field.
                if ('total-sun-panels' == $service->short) {
                    // will return the value and the year answer
                    $buildingServiceUpdateData['extra'] = $request->input('service.'.$service->id.'.extra');
                }
                // if its a ventilation, is has a dropdown so it has a serviceValue
                elseif ('house-ventilation' == $service->short) {
                    dd($request->input('service.'.$service->id.'.extra'));
                    $buildingServiceUpdateData['extra'] = $request->input('service.'.$service->id.'.extra');
                } else {

                    $buildingServiceUpdateData = ['service_value_id' => $serviceValueId];
                }

//                $building->buildingServices()->updateOrCreate(['service_id' => $service->id], ['service_value_id' => $serviceValueId]);


            }
        }

        $building->buildingFeatures()->updateOrCreate([], $request->input('building_features'));
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
