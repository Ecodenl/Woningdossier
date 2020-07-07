<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MigrateDemandDrivenServiceValueToExtraColumnOnBuildingServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // migration to migrate the service value from the "demand driven" option to the extra column.
        // when that option is selected we will change the value to the "mechanical extraction" and demand_driven set to true in the extra column.
        $ventilationServiceValues = DB::table('services')
            ->select('service_values.*')
            ->where('short', 'house-ventilation')
            ->leftJoin('service_values', 'services.id', '=', 'service_values.service_id')
            ->get();

        if ($ventilationServiceValues->count() > 0) {


            $demandDrivenServiceValue = $ventilationServiceValues->where('calculate_value', 5)->first();
            $mechanicalExtractionServiceValue = $ventilationServiceValues->where('calculate_value', 2)->first();

            // now update the rows where the service value is the demand driven one and change it to the mechanical extraction one
            // with demand driven set to true
            DB::table('building_services')
                ->where('service_id', $demandDrivenServiceValue->service_id)
                ->update(['extra' => null]);

            DB::table('building_services')
                ->where('service_id', $demandDrivenServiceValue->service_id)
                ->where('service_value_id', $demandDrivenServiceValue->id)
                ->update([
                    'service_value_id' => $mechanicalExtractionServiceValue->id,
                    'extra' => json_encode([
                        'demand_driven' => true,
                    ]),
                ]);
        }
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
