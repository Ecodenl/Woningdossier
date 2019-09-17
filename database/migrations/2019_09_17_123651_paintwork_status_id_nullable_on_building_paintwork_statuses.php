<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PaintworkStatusIdNullableOnBuildingPaintworkStatuses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('building_paintwork_statuses', function (Blueprint $table) {
            $table->integer('paintwork_status_id')->nullable()->unsigned()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $buildingPaintworkStatuses = DB::table('building_paintwork_statuses')->where('paintwork_status_id', null)->get();
        $buildingPaintworkStatusNo = DB::table('paintwork_statuses')->where('calculate_value', 7)->first();

        foreach ($buildingPaintworkStatuses as $buildingPaintworkStatus) {
            DB::table('building_paintwork_statuses')->where('id', $buildingPaintworkStatus->id)
                ->update([
                    'paintwork_status_id' => $buildingPaintworkStatusNo->id
                ]);
        }

        Schema::table('building_paintwork_statuses', function (Blueprint $table) {
            $table->integer('paintwork_status_id')->nullable(false)->unsigned()->change();
        });
    }
}
