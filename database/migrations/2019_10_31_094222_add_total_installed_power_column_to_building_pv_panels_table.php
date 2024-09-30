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
        Schema::table('building_pv_panels', function (Blueprint $table) {
            $table->integer('total_installed_power')->after('input_source_id')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('building_pv_panels', function (Blueprint $table) {
            $table->dropColumn('total_installed_power');
        });
    }
};
