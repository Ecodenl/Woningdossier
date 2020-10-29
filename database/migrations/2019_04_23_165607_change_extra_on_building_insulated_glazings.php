<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeExtraOnBuildingInsulatedGlazings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('building_insulated_glazings', function (Blueprint $table) {
            $table->text('extra')->nullable()->default(null)->change();
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
            $table->string('extra')->nullable()->default(null)->change();
        });
    }
}
