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

            $table->integer('address_id')->unsigned()->nullable()->default(null);
            $table->foreign('address_id')->references('id')->on('addresses') ->onDelete('restrict');

            $table->integer('object_type')->unsigned()->nullable()->default(null);
            $table->foreign('object_type')->references('id')->on('object_types') ->onDelete('restrict');

            $table->integer('building_category')->unsigned()->nullable()->default(null);
            $table->foreign('building_category')->references('id')->on('building_types') ->onDelete('restrict');

            $table->integer('building_type')->unsigned()->nullable()->default(null);
            $table->foreign('building_type')->references('id')->on('building_categories') ->onDelete('restrict');

            $table->integer('roof_type')->unsigned()->nullable()->default(null);
            $table->foreign('roof_type')->references('id')->on('roof_types') ->onDelete('restrict');

            $table->integer('energy_label')->unsigned()->nullable()->default(null);
            $table->foreign('energy_label')->references('id')->on('energy_labels') ->onDelete('restrict');

            $table->integer('surface')->nullable()->default(null);
            $table->integer('volume')->nullable()->default(null);
            $table->integer('build_year')->nullable()->default(null);
            $table->integer('building_layers')->nullable()->default(null);
            $table->boolean('monument')->default(false);

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
        Schema::dropIfExists('address_features');
    }
}
