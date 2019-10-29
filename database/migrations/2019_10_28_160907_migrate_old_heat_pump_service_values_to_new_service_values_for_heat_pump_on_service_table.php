<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MigrateOldHeatPumpServiceValuesToNewServiceValuesForHeatPumpOnServiceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // get the new heat pump service values
        $heatPumpServiceValues = \DB::table('services')
            ->select('service_values.*')
            ->where('short', 'heat-pump')
            ->leftJoin('service_values', 'services.id','=', 'service_values.service_id')
            ->get()->keyBy('calculate_value');

        // get the old heat pump services
        $fullHeatPumpService = \DB::table('services')->where('short', 'full-heat-pump')->first();
        $hybridHeatPump = \DB::table('services')->where('short', 'hybrid-heat-pump')->first();

        $buildings = \DB::table('buildings')->get();
        // modify data for all the buildings
        foreach ($buildings as $building) {
            $this->line('-------------------------------------------------');
            $this->line('migrating data for building id: '.$building->id);
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

                // when nothing its selected its a example building input source or some old account, skip it.
                if (is_null($answerForHybridHeatPump->service_value_id) &&  is_null($answerForFullHeatPump->service_value_id)) {
                    $this->line('service_value_id is empty for the hybrid and full heat pump on building id :'.$building->id);
                    $this->line('continue...');
                    continue;
                }
                // get the calculate values for the selected service values
                $calculateValueForHybridHeatPump = \DB::table('service_values')->where('id', $answerForHybridHeatPump->service_value_id)->first()->calculate_value;
                $calculateValueForFullHeatPump = \DB::table('service_values')->where('id', $answerForFullHeatPump->service_value_id)->first()->calculate_value;

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

                $this->line('the determined calculate value will be: '.$newCalculateValueForAnswer);
                $this->line('the new service value id will be: '.$heatPumpServiceValues[$newCalculateValueForAnswer]->id);


                // and insert the new rows.
                \DB::table('building_services')->insert([
                    'building_id' => $building->id,
                    'input_source_id' => $inputSourceId,
                    'service_id' => $heatPumpServiceValues->first()->service_id,
                    'service_value_id' => $heatPumpServiceValues[$newCalculateValueForAnswer]->id
                ]);
            }
        }

        // and delete all the old hybrid and full heat pump building services.
        \DB::table('building_services')
            ->where('service_id', $fullHeatPumpService->id)
            ->orWhere('service_id', $hybridHeatPump->id)
            ->delete();
    }

    private function line($msg)
    {
        echo "{$msg} \r\n";
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
