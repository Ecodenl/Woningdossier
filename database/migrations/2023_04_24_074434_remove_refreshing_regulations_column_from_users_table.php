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
        if (Schema::hasColumn('users', 'refreshing_regulations')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('refreshing_regulations');
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
        if (! Schema::hasColumn('users', 'refreshing_regulations')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('refreshing_regulations')->after('regulations_refreshed_at')->nullable()->default(false);
            });
        }
    }
};
