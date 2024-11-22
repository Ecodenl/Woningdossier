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
        Schema::create('completed_sub_steps', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedInteger('input_source_id');
            $table->foreign('input_source_id')->references('id')->on('input_sources')->onDelete('cascade');

            $table->unsignedInteger('building_id');
            $table->foreign('building_id')->references('id')->on('buildings')->onDelete('cascade');

            $table->unsignedBigInteger('sub_step_id');
            $table->foreign('sub_step_id')->references('id')->on('sub_steps')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('completed_sub_steps');
    }
};
