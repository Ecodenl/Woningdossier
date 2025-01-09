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
        Schema::create('building_heating_applications', function (Blueprint $table) {
            $table->increments('id');
            $table->json('name');
            $table->string('short');
            $table->smallInteger('calculate_value');
            $table->smallInteger('order');
            $table->timestamps();
        });

        Schema::table('building_features', function (Blueprint $table) {
            $table->foreign('building_heating_application_id')->references('id')->on('building_heating_applications')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('building_features', function (Blueprint $table) {
            $table->dropForeign(['building_heating_application_id']);
        });
        Schema::dropIfExists('building_heating_applications');
    }
};
