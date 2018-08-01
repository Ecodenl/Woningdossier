<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddInsulationSurfaceColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('building_features', function (Blueprint $table) {
            // add insulation_wall_surface to the building feature table
            $table->decimal('insulation_wall_surface')->after('wall_surface')->nullable()->default(null);
            // add insulation_floor_surface to the building feature table
            $table->decimal('insulation_floor_surface')->after('floor_surface')->nullable()->default(null);
        });
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
