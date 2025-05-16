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
        Schema::create('example_building_contents', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('example_building_id')->unsigned();
            $table->foreign('example_building_id')->references('id')->on('example_buildings')->onDelete('restrict');

            $table->integer('build_year')->nullable();
            $table->text('content')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('example_building_contents');
    }
};
