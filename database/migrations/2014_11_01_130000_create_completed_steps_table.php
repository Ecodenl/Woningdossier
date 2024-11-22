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
        Schema::create('completed_steps', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('input_source_id')->nullable();
            $table->foreign('input_source_id')->references('id')->on('input_sources')->nullOnDelete();

            $table->integer('building_id')->unsigned();
            $table->foreign('building_id')->references('id')->on('buildings')->onDelete('restrict');

            $table->integer('step_id')->unsigned();
            $table->foreign('step_id')->references('id')->on('steps')->onDelete('restrict');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('completed_steps');
    }
};
