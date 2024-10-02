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
        if (! Schema::hasColumn('buildings', 'municipality_id')) {
            Schema::table('buildings', function (Blueprint $table) {
                $table->unsignedBigInteger('municipality_id')->after('user_id')->nullable()->default(null);
                $table->foreign('municipality_id')->references('id')->on('municipalities')->nullOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('buildings', 'municipality_id')) {
            Schema::table('buildings', function (Blueprint $table) {
                $table->dropForeign(['municipality_id']);
                $table->dropColumn('municipality_id');
            });
        }
    }
};
