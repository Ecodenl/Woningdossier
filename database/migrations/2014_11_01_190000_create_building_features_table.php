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
        Schema::create('building_features', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('building_id')->unsigned()->nullable();
            $table->foreign('building_id')->references('id')->on('buildings')->onDelete('restrict');

            $table->integer('input_source_id')->unsigned()->nullable()->default(1);
            $table->foreign('input_source_id')->references('id')->on('input_sources')->onDelete('cascade');

            $table->unsignedInteger('example_building_id')->nullable();
            $table->foreign('example_building_id')->references('id')->on('example_buildings')->onDelete('set null');

            $table->integer('building_category_id')->unsigned()->nullable();
            $table->foreign('building_category_id')->references('id')->on('building_categories')->onDelete('restrict');

            // NOTE: Building type categories table gets created later, so foreign is added on
            // the '2021_08_27_092617_create_building_type_categories_table' migration.
            $table->unsignedBigInteger('building_type_category_id')->nullable();
            //$table->foreign('building_type_category_id')->references('id')->on('building_type_categories')->onDelete('restrict');

            $table->integer('building_type_id')->unsigned()->nullable();
            $table->foreign('building_type_id')->references('id')->on('building_types')->onDelete('restrict');

            $table->integer('roof_type_id')->unsigned()->nullable();
            $table->foreign('roof_type_id')->references('id')->on('roof_types')->onDelete('restrict');

            $table->integer('energy_label_id')->unsigned()->nullable();
            $table->foreign('energy_label_id')->references('id')->on('energy_labels')->onDelete('restrict');

            $table->integer('cavity_wall')->nullable();

            $table->decimal('wall_surface')->nullable();
            $table->decimal('insulation_wall_surface')->nullable();
            $table->integer('facade_plastered_painted')->nullable();

            $table->integer('wall_joints')->unsigned()->nullable();
            $table->foreign('wall_joints')->references('id')->on('facade_surfaces')->onDelete('restrict');

            $table->integer('contaminated_wall_joints')->unsigned()->nullable();
            $table->foreign('contaminated_wall_joints')->references('id')->on('facade_surfaces')->onDelete('restrict');

            $table->integer('element_values')->unsigned()->nullable();
            $table->foreign('element_values')->references('id')->on('element_values')->onDelete('restrict');

            $table->integer('facade_plastered_surface_id')->unsigned()->nullable();
            $table->foreign('facade_plastered_surface_id')->references('id')->on('facade_plastered_surfaces')->onDelete('restrict');

            $table->integer('facade_damaged_paintwork_id')->unsigned()->nullable();
            $table->foreign('facade_damaged_paintwork_id')->references('id')->on('facade_damaged_paintworks')->onDelete('restrict');

            $table->decimal('surface')->nullable();
            $table->decimal('floor_surface')->nullable();
            $table->decimal('insulation_surface')->nullable();
            $table->decimal('window_surface')->nullable();

            $table->integer('volume')->nullable();
            $table->integer('build_year')->nullable();
            $table->integer('building_layers')->nullable();
            $table->unsignedInteger('monument')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('building_features');
    }
};
