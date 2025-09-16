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
        Schema::create('integration_processes', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Integration::class, 'integration_id')->constrained();
            // Can't use foreignIdFor for building, as the building has an unsignedInteger, and foreignIdFor
            // automates to unsignedBigInteger.
            //$table->foreignIdFor(\App\Models\Building::class, 'building_id')->constrained();
            $table->unsignedInteger('building_id');
            $table->foreign('building_id')->references('id')->on('buildings')->cascadeOnDelete();
            $table->string('process');
            $table->dateTime('synced_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('integration_processes');
    }
};
