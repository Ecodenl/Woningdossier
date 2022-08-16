<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBuildingTypeCategoryIdToBuildingFeaturesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Note: We cannot delete this because the original table cannot create the foreign key constraint (because
        // the categories table doesn't exist yet at that point)
        Schema::table('building_features', function (Blueprint $table) {
            $table->unsignedBigInteger('building_type_category_id')->nullable()->after('building_category_id');
            $table->foreign('building_type_category_id')->references('id')->on('building_type_categories')->onDelete('restrict');
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
            $table->dropForeign('building_features_building_type_category_id_foreign');
            $table->dropColumn('building_type_category_id');
        });
    }
}
