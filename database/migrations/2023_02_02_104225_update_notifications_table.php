<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('notifications', 'uuid')) {
            DB::table('notifications')->truncate();

            Schema::table('notifications', function (Blueprint $table) {
                $table->uuid('uuid')->after('type');
                $table->dropColumn('is_active', 'active_count');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // A rollback won't bring back the data
        if (Schema::hasColumn('notifications', 'uuid')) {
            Schema::table('notifications', function (Blueprint $table) {
                $table->boolean('is_active')->after('type');
                $table->unsignedInteger('active_count')->default(0)->after('is_active');

                $table->dropColumn('uuid');
            });
        };
    }
};
