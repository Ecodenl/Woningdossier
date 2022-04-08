<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class IntToBigintForIdColumnOnVariousTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tables = [
            'building_elements',
            'building_services',
            'user_interests',
            'user_action_plan_advices',
        ];
        Schema::table('appliance_building_services', function ($table) {
            $table->dropForeign(['building_service_id']);
        });
        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->bigIncrements('id')->change();
            });
        }

        Schema::table('appliance_building_services', function (Blueprint $table) {
            $table->unsignedBigInteger('building_service_id')->change();
            $table->foreign('building_service_id')->on('building_services')->references('id')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $tables = [
            'building_elements',
            'building_services',
            'user_interests',
            'user_action_plan_advices',
        ];
        Schema::table('appliance_building_services', function ($table) {
            $table->dropForeign(['building_service_id']);
        });
        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->unsignedInteger('id')->change();
            });
        }

        Schema::table('appliance_building_services', function (Blueprint $table) {
            $table->unsignedInteger('building_service_id')->change();
            $table->foreign('building_service_id')->on('building_services')->references('id')->onDelete('cascade');
        });
    }
}
