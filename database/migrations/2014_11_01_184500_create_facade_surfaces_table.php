<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFacadeSurfacesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('facade_surfaces', function (Blueprint $table) {
            $table->increments('id');
            $table->json('name');
            $table->integer('calculate_value')->nullable();
            $table->integer('order');
            $table->json('execution_term_name');
            $table->integer('term_years')->nullable();
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
        Schema::dropIfExists('facade_surfaces');
    }
}
