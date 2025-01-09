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
        Schema::create('dossier_settings', function (Blueprint $table) {
            $table->id();

            $table->unsignedInteger('input_source_id')->nullable();
            $table->foreign('input_source_id')->references('id')->on('input_sources')->nullOnDelete();

            $table->unsignedInteger('building_id')->unsigned();
            $table->foreign('building_id')->references('id')->on('buildings')->cascadeOnDelete();

            $table->string('type');

            $table->timestamp('done_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dossier_settings');
    }
};
