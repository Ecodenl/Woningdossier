<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // The package the last SmartTwin import put in the action plan, and what SmartTwin calculated
        // for it. One row per building: an import replaces the plan, and with it the package.
        Schema::create('smarttwin_packages', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('building_id')->unique();
            $table->foreign('building_id')->references('id')->on('buildings')->cascadeOnDelete();
            $table->unsignedInteger('input_source_id')->nullable();
            $table->foreign('input_source_id')->references('id')->on('input_sources')->nullOnDelete();
            $table->json('measure_application_ids');
            $table->json('current_result')->nullable();
            $table->json('scenario_result')->nullable();
            $table->decimal('total_cost_with_tax', 10, 2)->nullable();
            $table->decimal('energy_cost_savings_per_month', 10, 2)->nullable();
            $table->timestamp('imported_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('smarttwin_packages');
    }
};
