<?php

namespace App\Http\Controllers\Cooperation\Tool\GeneralData;

use App\Events\StepDataHasBeenChanged;
use App\Helpers\Hoomdossier;
use App\Helpers\HoomdossierSession;
use App\Helpers\StepHelper;
use App\Http\Requests\Cooperation\Tool\GeneralData\CurrentStateFormRequest;
use App\Models\BuildingHeatingApplication;
use App\Models\BuildingService;
use App\Models\Cooperation;
use App\Models\Element;
use App\Models\ElementValue;
use App\Models\ExampleBuildingContent;
use App\Models\InputSource;
use App\Models\Interest;
use App\Models\Service;
use App\Models\ServiceValue;
use App\Models\Step;
use App\Models\UserInterest;
use App\Http\Controllers\Controller;
use App\Services\StepCommentService;
use App\Services\UserInterestService;
use function Couchbase\defaultDecoder;
use Illuminate\Support\Arr;

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

    public function store(CurrentStateFormRequest $request)
    {
        $building = HoomdossierSession::getBuilding(true);
        $buildingOwner = $building->user;
        $inputSource = HoomdossierSession::getInputSource(true);
        $step = Step::findByShort('current-state');

        $userHasInterests = $buildingOwner->stepInterests()->exists();
        // save the building elements
        $elements = $request->get('elements', []);
        foreach ($elements as $elementShort => $elementData) {
            $building->buildingElements()->updateOrCreate(
                ['element_id' => $elementData['element_id']],
                ['element_value_id' => $elementData['element_value_id']]
            );
        }
        // when the user has no interests, we have to do some A.I to determine the interest of a user
        // basic rule: when something is "bad" the user has interest in the service / element
        if (!$userHasInterests) {
            $userInterests = [];
            $noInterestNotPossibleId = Interest::where('calculate_value', 5)->first()->id;
            $yesOnShortTermInterestId = Interest::where('calculate_value', 1)->first()->id;

            $sleepingRoomsWindows = $elements['sleeping-rooms-windows'];
            $livingRoomsWindows = $elements['living-rooms-windows'];

            // no calc values for these things, so 1 and 2 are the ids where the glazing is bad.
            $userInterests['insulated-glazing'] = in_array($sleepingRoomsWindows['element_value_id'], [1, 2]) || in_array($livingRoomsWindows['element_value_id'], [1, 2]) ? $yesOnShortTermInterestId : $noInterestNotPossibleId;

            foreach (Arr::except($elements, ['living-rooms-windows', 'sleeping-rooms-windows', 'crack-sealing']) as $elementShort => $elementData) {
                // when its higher than 2, the insulation is not bad so not interest
                $userHasDecentInsulation = ElementValue::find($elementData['element_value_id'])->calculate_value > 2;
                $userInterests[$elementShort] = $userHasDecentInsulation ? $noInterestNotPossibleId : $yesOnShortTermInterestId;
            }
        }

        dd($userInterests);
//                UserInterestService::save($buildingOwner, $inputSource, Step::class, Step::findByShort('insulated-glazing'), );
        // save the building services
        $services = $request->get('services', []);
        foreach ($services as $serviceData) {
            $building->buildingServices()->updateOrCreate(
                ['service_id' => $serviceData['service_id']],
                array_except($serviceData, 'service_id')
            );
        }

        // save buildign features, pv panels and the comments
        $building->pvPanels()->updateOrCreate([], $request->input('building_pv_panels'));
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
