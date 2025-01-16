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
        Schema::create('building_services', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->integer('building_id')->unsigned()->nullable();
            $table->foreign('building_id')->references('id')->on('buildings')->onDelete('restrict');

            $table->integer('input_source_id')->unsigned()->nullable()->default(1);
            $table->foreign('input_source_id')->references('id')->on('input_sources')->onDelete('cascade');

            $table->integer('service_id')->unsigned();
            $table->foreign('service_id')->references('id')->on('services')->onDelete('restrict');

            $table->integer('service_value_id')->unsigned()->nullable();
            $table->foreign('service_value_id')->references('id')->on('service_values')->onDelete('restrict');

            $table->json('extra')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('building_services');
    }
};
