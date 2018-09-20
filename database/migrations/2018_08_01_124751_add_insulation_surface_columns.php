<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
            $table->decimal('insulation_surface')->after('surface')->nullable()->default(null);
        });

        // rename from surface to roof surface for better readability
        Schema::table('building_roof_types', function (Blueprint $table) {
            $table->renameColumn('surface', 'roof_surface');
        });

        // add insulation_roof_surface to the building feature table
        Schema::table('building_roof_types', function (Blueprint $table) {
            $table->integer('insulation_roof_surface')->after('roof_surface')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('building_features', function (Blueprint $table) {
            $table->dropColumn('insulation_wall_surface');
            $table->dropColumn('insulation_surface');
        });

        Schema::table('building_roof_types', function (Blueprint $table) {
            $table->renameColumn('roof_surface', 'surface');
        });

        Schema::table('building_roof_types', function (Blueprint $table) {
            $table->dropColumn('insulation_roof_surface');
        });
    }
}
