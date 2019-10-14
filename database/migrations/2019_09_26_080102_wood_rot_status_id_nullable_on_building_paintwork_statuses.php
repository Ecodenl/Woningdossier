<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class WoodRotStatusIdNullableOnBuildingPaintworkStatuses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('building_paintwork_statuses', function (Blueprint $table) {
            $table->integer('wood_rot_status_id')->nullable()->unsigned()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // We don't want to revert this
    }
}
