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
        Schema::create('mappings', function (Blueprint $table) {
            $table->id();
            $table->string('type')->nullable();
            $table->json('conditions')->nullable();

            $table->nullableMorphs('from_model');
            $table->string('from_value')->nullable();

            $table->nullableMorphs('target_model');
            $table->string('target_value')->nullable();
            $table->json('target_data')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mappings');
    }
};
