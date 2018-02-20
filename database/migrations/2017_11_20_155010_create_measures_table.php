<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMeasuresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('measures', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');

            $table->integer('service_type')->unsigned()->nullable()->default(null);
            $table->foreign('service_type')->references('id')->on('service_types') ->onDelete('restrict');

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
        Schema::dropIfExists('measures');
    }
}
