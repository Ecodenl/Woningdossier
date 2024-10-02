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
        if (!Schema::hasColumn('users', 'tool_last_changed_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dateTime('tool_last_changed_at')->nullable()->default(null)->after('allow_access');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('tool_last_changed_at');
        });
    }
};
