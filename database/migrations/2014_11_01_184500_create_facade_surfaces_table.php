<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
	        $table->uuid('name');
	        $table->integer('calculate_value')->nullable();
	        $table->integer('order');
	        $table->uuid('execution_term_name');
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
