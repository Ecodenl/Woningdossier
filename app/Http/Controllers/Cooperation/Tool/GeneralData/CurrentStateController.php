<?php

namespace App\Http\Controllers\Cooperation\Tool\GeneralData;

use App\Helpers\HoomdossierSession;
use App\Models\BuildingService;
use App\Models\Cooperation;
use App\Models\Element;
use App\Models\ExampleBuildingContent;
use App\Models\InputSource;
use App\Models\Service;
use App\Models\ServiceValue;
use App\Models\UserInterest;
use function Couchbase\defaultDecoder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CurrentStateController extends Controller
{
    public function index(Cooperation $cooperation)
    {
        $exampleBuildingContents = \DB::table('example_building_contents')->get();

        $heatPumpServiceValues = \DB::table('services')
            ->select('service_values.*')
            ->where('short', 'heat-pump')
            ->leftJoin('service_values', 'services.id','=', 'service_values.service_id')
            ->get()->keyBy('calculate_value');

        // get the old heat pump services
        $fullHeatPumpService = \DB::table('services')->where('short', 'full-heat-pump')->first();
        $hybridHeatPump = \DB::table('services')->where('short', 'hybrid-heat-pump')->first();

        $heatPumpService = \DB::table('services')->where('short', 'heat-pump')->first();
        // update all the content for the example buildings
        // we have to update the old hybrid/full heat pump service to the new heat-pump
        foreach ($exampleBuildingContents as $exampleBuildingContent) {
            $content = json_decode($exampleBuildingContent->content,true);
            if (array_key_exists('service', $content['general-data'])) {
                // collect some data and vars for later on
                $serviceContent = $content['general-data']['service'];

                if (isset($serviceContent[$fullHeatPumpService->id]) && $serviceContent[$hybridHeatPump->id]) {

                    // get the old service value id's
                    $fullHeatPumpServiceValueId = $serviceContent[$fullHeatPumpService->id];
                    $hybridHeatPumpServiceValueId = $serviceContent[$hybridHeatPump->id];
                    // now we can unset them
                    unset($serviceContent[$fullHeatPumpService->id], $serviceContent[$hybridHeatPump->id]);

                    // this will only occur on non filled example buildings
                    if (empty($fullHeatPumpServiceValueId) || empty($hybridHeatPumpServiceValueId)) {
                        $serviceContent[$heatPumpService->id] = $heatPumpServiceValues[1]->id;
                    } else {
                        // get the calculate values for the selected service values
                        $calculateValueForHybridHeatPump = \DB::table('service_values')->where('id', $hybridHeatPumpServiceValueId)->first()->calculate_value;
                        $calculateValueForFullHeatPump = \DB::table('service_values')->where('id', $fullHeatPumpServiceValueId)->first()->calculate_value;

                        // some A.I will happen here.
                        // when both the calc values for the hybrid and full heat pump are 1 they are left with the default values, so we will leave it like that
                        // when the calc value for the hybrid pump is set to two the user has an hybrid pump, so we will set it to 4 since that's the new calc value for the hybrid pump
                        // else we will get the calc value from the full heat pump, those stayed the same

                        if ($calculateValueForHybridHeatPump == 1 && $calculateValueForFullHeatPump == 1) {
                            $newCalculateValueForAnswer = 1;
                        } else if ($calculateValueForHybridHeatPump == 2) {
                            $newCalculateValueForAnswer = 4;
                        } else {
                            $newCalculateValueForAnswer = $calculateValueForFullHeatPump;
                        }

                        $serviceContent[$heatPumpService->id] = $heatPumpServiceValues[$newCalculateValueForAnswer]->id;
                    }
                    $content['general-data']['service'] = $serviceContent;

                    \DB::table('example_building_contents')
                        ->where('id', $exampleBuildingContent->id)
                        ->update([
                            'content' => json_encode($content)
                        ]);
                }
            }
        }

        dd('dum');

        $building = HoomdossierSession::getBuilding(true);
        $buildingOwner = $building->user;
        $userInterestsForMe = UserInterest::forMe()->get();

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

        return view('cooperation.tool.general-data.current-state.index', compact(
            'building', 'buildingOwner', 'elements', 'services', 'userInterestsForMe', 'services'
        ));
    }
}
