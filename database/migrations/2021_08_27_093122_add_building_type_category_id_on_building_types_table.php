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
