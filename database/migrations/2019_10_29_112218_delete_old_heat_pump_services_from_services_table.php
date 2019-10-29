<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DeleteOldHeatPumpServicesFromServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
//        $serviceIdsToDelete = \DB::table('services')
//            ->whereIn('short', ['hybrid-heat-pump', 'full-heat-pump'])
//            ->select('id')
//            ->get()->pluck('id')
//            ->toArray();
//
//        // delete the service values
//        DB::table('service_values')->whereIn(
//            'service_id', $serviceIdsToDelete
//        )->delete();
//
//        // and delete the services itself
//        DB::table('services')->whereIn('short', ['hybrid-heat-pump', 'full-heat-pump'])->delete();
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
