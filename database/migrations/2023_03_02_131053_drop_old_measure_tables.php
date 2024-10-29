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
        // there are various old tables that have the name "measure" in them.
        // currently they serve no use and are only cluttter.
        // here we will drop all those tables.
        Schema::dropIfExists('measure_properties');
        Schema::dropIfExists('measure_properties');
        Schema::dropIfExists('measure_service_type');
        Schema::dropIfExists('measure_measure_category');

        Schema::dropIfExists('devices');

        Schema::dropIfExists('measures');

        Schema::dropIfExists('device_types');
        Schema::dropIfExists('device_options');

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
