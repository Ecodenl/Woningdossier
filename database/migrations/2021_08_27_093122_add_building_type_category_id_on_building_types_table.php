<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBuildingTypeCategoryIdOnBuildingTypesTable extends Migration
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
        Schema::table('building_types', function (Blueprint $table) {
            $table->unsignedBigInteger('building_type_category_id')->nullable()->default(null)->after('id');
            $table->foreign('building_type_category_id')->references('id')->on('building_type_categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('building_types', function (Blueprint $table) {
            $table->dropForeign(['building_type_category_id']);
            $table->dropColumn('building_type_category_id');
        });
    }
}