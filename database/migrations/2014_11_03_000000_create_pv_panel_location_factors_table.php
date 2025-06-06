<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePvPanelLocationFactorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pv_panel_location_factors', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('pc2')->unsigned();
            $table->string('location');
            $table->string('country')->default(\App\Enums\Country::COUNTRY_NL);
            $table->decimal('factor', 3, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pv_panel_location_factors');
    }
}
