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
        Schema::create('appliance_building_services', function (Blueprint $table) {
            $table->integer('appliance_id')->unsigned()->nullable()->default(null);
            $table->foreign('appliance_id')->references('id')->on('appliances')->onDelete('restrict');

            $table->integer('building_service_id')->unsigned()->nullable()->default(null);
            $table->foreign('building_service_id')->references('id')->on('building_services')->onDelete('restrict');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *s.
     */
    public function down(): void
    {
        Schema::dropIfExists('appliance_building_services');
    }
};
