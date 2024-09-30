<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        if (! Schema::hasColumn('building_coach_statuses', 'appointment_date')) {
            Schema::table('building_coach_statuses',
                function (Blueprint $table) {
                    $table->dateTime('appointment_date')->nullable()->after('status');
                });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        if (Schema::hasColumn('building_coach_statuses', 'appointment_date')) {
            // just in case
            Schema::table('building_coach_statuses',
                function (Blueprint $table) {
                    $table->dropColumn('appointment_date');
                });
        }
    }
};
