<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('building_roof_types', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('building_id')->unsigned();
            $table->foreign('building_id')->references('id')->on('buildings')->onDelete('restrict');

            $table->integer('roof_type_id')->unsigned();
            $table->foreign('roof_type_id')->references('id')->on('roof_types')->onDelete('restrict');

            $table->integer('element_value_id')->unsigned()->nullable();
            $table->foreign('element_value_id')->references('id')->on('element_values')->onDelete('restrict');

            $table->integer('surface')->unsigned()->nullable()->default(null);
            $table->integer('zinc_surface')->unsigned()->nullable()->default(null);

            $table->integer('building_heating_id')->unsigned()->nullable();
            $table->foreign('building_heating_id')->references('id')->on('building_heatings')->onDelete('restrict');

            $table->text('extra')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('building_roof_types');
    }
};
