<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('building_type_element_max_savings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('building_type_id')->unsigned();
            $table->foreign('building_type_id')->references('id')->on('building_types');
            $table->integer('element_id')->unsigned();
            $table->foreign('element_id')->references('id')->on('elements');
            $table->integer('max_saving')->unsigned();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('building_type_element_max_savings');
    }
};
