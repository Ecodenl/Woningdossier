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
        Schema::create('building_pv_panels', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('building_id')->unsigned();
            $table->foreign('building_id')->references('id')->on('buildings')->onDelete('restrict');

            $table->integer('input_source_id')->unsigned()->nullable()->default(1);
            $table->foreign('input_source_id')->references('id')->on('input_sources')->onDelete('cascade');

            $table->integer('total_installed_power')->nullable();

            $table->integer('peak_power')->unsigned()->nullable();
            $table->integer('number')->default(0);

            $table->integer('pv_panel_orientation_id')->unsigned()->nullable();
            $table->foreign('pv_panel_orientation_id')->references('id')->on('pv_panel_orientations')->onDelete('set null');

            $table->integer('angle')->unsigned()->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('building_pv_panels');
    }
};
