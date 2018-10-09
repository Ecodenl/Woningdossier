<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBuildingNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('building_notes', function (Blueprint $table) {
            $table->increments('id');

            $table->string('street')->default('');
            $table->string('number')->default('');
            $table->string('extension')->default('');
            $table->string('city')->default('');
            $table->string('postal_code')->default('');
            $table->string('country_code', 2)->default('nl');
            $table->string('bag_addressid')->default('');

            $table->longText('notes');

            $table->integer('building_id')->unsigned()->nullable();
            $table->foreign('building_id')->references('id')->on('buildings')->onDelete('set null');

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
        Schema::dropIfExists('building_notes');
    }
}
