<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('building_roof_types', function (Blueprint $table) {
            // Same as the wall and floor surfaces on building_features. As integers, MySQL rounded
            // every roof surface to whole square metres: 39,75 from SmartTwin, or 68,6 from an
            // example building, was stored as 40 and 69. Existing values convert without loss.
            // Not unsigned: deprecated for DECIMAL in MySQL 8, and the other surfaces are signed too.
            $table->decimal('roof_surface')->nullable()->change();
            $table->decimal('insulation_roof_surface')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('building_roof_types', function (Blueprint $table) {
            $table->integer('roof_surface')->unsigned()->nullable()->change();
            $table->integer('insulation_roof_surface')->nullable()->change();
        });
    }
};
