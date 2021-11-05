<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MoveExampleBuildingIdToBuildingFeaturesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $buildings = DB::table('buildings')->get();

        Schema::table('building_features', function (Blueprint $table) {
            $table->unsignedInteger('example_building_id')->nullable()->after('input_source_id')->default(null);
            $table->foreign('example_building_id')->references('id')->on('example_buildings')->onDelete('set null');
        });

        foreach ($buildings as $building) {
            // we just get all the building features for the building and give them the example building.
            DB::table('building_features')
                ->where('building_id', $building->id)
                ->update(['example_building_id' => $building->example_building_id]);
        }

        Schema::table('buildings', function (Blueprint $table) {
            $table->dropForeign(['example_building_id']);
            $table->dropColumn('example_building_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // we got to do a little group because the building features actually holds a input source.
        $buildingFeatures = DB::table('building_features')
            ->select(['building_id', 'example_building_id'])
            ->groupBy(['building_id', 'example_building_id'])
            ->whereNotNull('example_building_id')
            ->get();

        Schema::table('buildings', function (Blueprint $table) {
            $table->unsignedInteger('example_building_id')->nullable()->after('bag_addressid')->default(null);
            $table->foreign('example_building_id')->references('id')->on('example_buildings')->onDelete('set null');
        });

        foreach ($buildingFeatures as $buildingFeature) {
            // we just get all the building features for the building and give them the example building.
            DB::table('buildings')
                ->where('id', $buildingFeature->building_id)
                ->update(['example_building_id' => $buildingFeature->example_building_id]);
        }

        Schema::table('building_features', function (Blueprint $table) {
            $table->dropForeign(['example_building_id']);
            $table->dropColumn('example_building_id');
        });
    }
}
