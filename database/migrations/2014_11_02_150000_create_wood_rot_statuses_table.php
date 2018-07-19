<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWoodRotStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wood_rot_statuses', function (Blueprint $table) {
	        $table->increments('id');
	        $table->uuid('name');
	        $table->integer('calculate_value')->nullable();
	        $table->integer('order');
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
        Schema::dropIfExists('wood_rot_statuses');
    }
}
