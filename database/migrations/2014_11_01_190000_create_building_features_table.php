<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBuildingFeaturesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('building_features', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('building_id')->unsigned()->nullable()->default(null);
            $table->foreign('building_id')->references('id')->on('buildings')->onDelete('restrict');

            $table->integer('building_category_id')->unsigned()->nullable()->default(null);
            $table->foreign('building_category_id')->references('id')->on('building_categories')->onDelete('restrict');

            $table->integer('building_type_id')->unsigned()->nullable()->default(null);
            $table->foreign('building_type_id')->references('id')->on('building_types')->onDelete('restrict');

            $table->integer('roof_type_id')->unsigned()->nullable()->default(null);
            $table->foreign('roof_type_id')->references('id')->on('roof_types')->onDelete('restrict');

            $table->integer('energy_label_id')->unsigned()->nullable()->default(null);
            $table->foreign('energy_label_id')->references('id')->on('energy_labels')->onDelete('restrict');

            $table->integer('cavity_wall')->nullable()->default(null);

            $table->integer('wall_surface')->nullable()->default(null);
            $table->integer('facade_plastered_painted')->nullable()->default(null);

            $table->integer('wall_joints')->unsigned()->nullable()->default(null);
            $table->foreign('wall_joints')->references('id')->on('facade_surfaces')->onDelete('restrict');

            $table->integer('contaminated_wall_joints')->unsigned()->nullable()->default(null);
            $table->foreign('contaminated_wall_joints')->references('id')->on('facade_surfaces')->onDelete('restrict');

            $table->integer('element_values')->unsigned()->nullable()->default(null);
            $table->foreign('element_values')->references('id')->on('element_values')->onDelete('restrict');

            $table->integer('facade_plastered_surface_id')->unsigned()->nullable()->default(null);
            $table->foreign('facade_plastered_surface_id')->references('id')->on('facade_plastered_surfaces')->onDelete('restrict');

			$table->integer('facade_damaged_paintwork_id')->unsigned()->nullable()->default(null);
			$table->foreign('facade_damaged_paintwork_id')->references('id')->on('facade_damaged_paintworks')->onDelete('restrict');

            $table->decimal('surface')->nullable()->default(null);
            $table->decimal('window_surface')->nullable()->default(null);
            $table->integer('volume')->nullable()->default(null);
            $table->integer('build_year')->nullable()->default(null);
            $table->integer('building_layers')->nullable()->default(null);
            $table->boolean('monument')->default(false);
            $table->longText('additional_info')->nullable()->default(null);

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
        Schema::dropIfExists('building_features');
    }
}
