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
        if (! Schema::hasColumn('cooperation_measure_applications', 'is_deletable')) {
            Schema::table('cooperation_measure_applications', function (Blueprint $table) {
                $table->boolean('is_extensive_measure')->default(false)->after('extra');
                $table->boolean('is_deletable')->default(false)->after('is_extensive_measure');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('cooperation_measure_applications', 'is_deletable')) {
            Schema::table('cooperation_measure_applications', function (Blueprint $table) {
                $table->dropColumn('is_extensive_measure', 'is_deletable');
            });
        }
    }
};
