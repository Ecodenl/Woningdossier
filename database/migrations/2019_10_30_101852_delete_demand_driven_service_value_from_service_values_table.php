<?php

use Illuminate\Database\Migrations\Migration;

class DeleteDemandDrivenServiceValueFromServiceValuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $ventilationServiceValue = DB::table('services')
            ->where('short', 'house-ventilation')->first();

        if ($ventilationServiceValue instanceof \stdClass) {
            DB::table('service_values')
                ->where('service_id', $ventilationServiceValue->id)
                ->where('calculate_value', 5)
               ->delete();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
