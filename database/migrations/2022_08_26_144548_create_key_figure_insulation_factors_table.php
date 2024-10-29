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
        Schema::create('key_figure_insulation_factors', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->decimal('insulation_grade', 4, 2);
            $table->decimal('insulation_factor', 4, 2);
            $table->unsignedInteger('energy_consumption_per_m2');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('key_figure_insulation_factors');
    }
};
