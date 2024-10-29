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
        if (Schema::hasColumn('logs', 'for_user_id')) {
            Schema::table('logs', function (Blueprint $table) {
                $table->dropForeign(['for_user_id']);
                $table->dropColumn('for_user_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasColumn('logs', 'for_user_id')) {
            Schema::table('logs', function (Blueprint $table) {
                $table->integer('for_user_id')->after('building_id')->nullable()->default(null)->unsigned();
                $table->foreign('for_user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }
    }
};
