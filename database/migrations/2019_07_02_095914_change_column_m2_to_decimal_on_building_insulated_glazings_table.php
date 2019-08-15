<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeColumnM2ToDecimalOnBuildingInsulatedGlazingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('building_insulated_glazings', function (Blueprint $table) {
            $table->decimal('m2')->unsigned()->nullable()->default(null)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('building_insulated_glazings', function (Blueprint $table) {
            $table->integer('m2')->unsigned()->nullable()->default(null);
        });
    }
}
