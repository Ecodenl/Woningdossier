<?php

namespace App\Http\Controllers\Cooperation\Tool\GeneralData;

use App\Helpers\HoomdossierSession;
use App\Models\Cooperation;
use App\Models\Element;
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
        $heatPumpService = \DB::table('services')->where('short', 'heat-pump')->first();
        // the old services
        $fullHeatPumpService = \DB::table('services')->where('short', 'full-heat-pump')->first();
        $hybridHeatPump = \DB::table('services')->where('short', 'hybrid-heat-pump')->first();
//        $serviceIds = $services->pluck('id')->toArray();

        $buildings = \DB::table('buildings')->get();

        foreach ($buildings as $building) {
            // get the full and hybrid heat pump values for a building
            $buildingServicesForAllHeatPumps = \DB::table('building_services')

                ->where('building_id', $building->id)
                ->where(function ($query) use ($fullHeatPumpService, $hybridHeatPump) {
                    $query->where('service_id', $fullHeatPumpService->id)
                        ->orWhere('service_id', $hybridHeatPump->id);
                })->get();



            // group them by the input source id, we will have to determine the service_value_id for each input source
            $buildingServicesForHeatPumpGroupedByInputSourceId = [];
            foreach ($buildingServicesForAllHeatPumps as $buildingServiceForHeatPump) {
                $buildingServicesForHeatPumpGroupedByInputSourceId[$buildingServiceForHeatPump->input_source_id][$buildingServiceForHeatPump->service_id] = $buildingServiceForHeatPump;
            }

            foreach ($buildingServicesForHeatPumpGroupedByInputSourceId as $inputSourceId => $buildingServicesForHeatPumps) {
                $answerForHybridHeatPump = $buildingServicesForHeatPumps[$hybridHeatPump->id];
                $answerForFullHeatPump = $buildingServicesForHeatPumps[$fullHeatPumpService->id];

                // todo: some exception occurs.
                // get the calculate values for the selected service values
                $calculateValueForHybridHeatPump = \DB::table('service_values')->where('id', $answerForHybridHeatPump->service_value_id)->first()->calculate_value;
                $calculateValueForFullHeatPump = \DB::table('service_values')->where('id', $answerForFullHeatPump->service_value_id)->first()->calculate_value;

                // in this case the user did not select any value so we will just leave it like that
                if ($calculateValueForHybridHeatPump == 1 && $calculateValueForFullHeatPump == 1) {
                    $newCalculateValueForAnswer = 1;


                } else if ($calculateValueForHybridHeatPump == 2) {
                    // since the calculate value for the hybrid pump changed assign it manually
                    $newCalculateValueForAnswer = 4;
                } else {
                    // no custom answe for the hybrid pump was provider so we will take the one from the full heat pump
                    $newCalculateValueForAnswer = $calculateValueForFullHeatPump;
                }

                dump($newCalculateValueForAnswer);
            }
        }




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
