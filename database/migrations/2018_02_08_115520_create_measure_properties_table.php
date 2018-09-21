<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMeasurePropertiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('measure_properties', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('measure_id')->unsigned()->nullable()->default(null);
            $table->foreign('measure_id')->references('id')->on('measures')->onDelete('restrict');

            $table->string('name')->default('');
            $table->string('value')->default('');
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
        Schema::dropIfExists('measure_properties');
    }
}
