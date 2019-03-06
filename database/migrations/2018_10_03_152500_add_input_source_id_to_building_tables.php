<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInputSourceIdToBuildingTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $buildingTables = [
            'building_user_usages',
            'building_elements',
            'building_insulated_glazings',
            'building_services',
            'building_appliances',
            'building_pv_panels',
            'building_paintwork_statuses',
            'devices',
            'building_roof_types',
            'building_heaters',
            'building_features',
        ];

        foreach ($buildingTables as $buildingTable) {
            Schema::table($buildingTable, function (Blueprint $table) {
                $table->integer('input_source_id')->unsigned()->nullable()->default(1)->after('building_id');
                $table->foreign('input_source_id')->references('id')->on('input_sources')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $buildingTables = [
            'building_user_usages',
            'building_elements',
            'building_insulated_glazings',
            'building_services',
            'building_appliances',
            'building_pv_panels',
            'building_paintwork_statuses',
            'devices',
            'building_roof_types',
            'building_heaters',
            'building_features',
        ];

        foreach ($buildingTables as $buildingTable) {
            Schema::table($buildingTable,
                function (Blueprint $table) {
                    $table->dropForeign($table->getTable().'_input_source_id_foreign');
                    $table->dropColumn('input_source_id');
                });
        }
    }
}
