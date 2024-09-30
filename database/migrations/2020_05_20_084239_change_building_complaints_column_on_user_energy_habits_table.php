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
        Schema::table('user_energy_habits', function (Blueprint $table) {
            $table->longText('building_complaints')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_energy_habits', function (Blueprint $table) {
            $table->string('building_complaints')->change();
        });
    }
};
