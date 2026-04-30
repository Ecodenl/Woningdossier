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
        Schema::table('cooperation_scan', function (Blueprint $table) {
            $table->boolean('small_measures_enabled')->default(true)->after('scan_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cooperation_scan', function (Blueprint $table) {
            $table->dropColumn('small_measures_enabled');
        });
    }
};
