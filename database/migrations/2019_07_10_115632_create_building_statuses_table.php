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
        Schema::create('building_statuses', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('building_id');
            $table->foreign('building_id')->references('id')->on('buildings')->onDelete('cascade');

            $table->unsignedInteger('status_id');
            $table->foreign('status_id')->references('id')->on('statuses')->onDelete('cascade');

            $table->dateTime('appointment_date')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('building_statuses');
    }
};
