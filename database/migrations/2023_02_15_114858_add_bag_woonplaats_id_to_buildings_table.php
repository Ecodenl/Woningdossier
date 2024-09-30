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
        if ( ! Schema::hasColumn('buildings', 'bag_woonplaats_id')) {
            Schema::table('buildings', function (Blueprint $table) {
                $table->string('bag_woonplaats_id')->after('bag_addressid')->nullable()->default(null);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('buildings', 'bag_woonplaats_id')) {
            Schema::table('buildings', function (Blueprint $table) {
                $table->dropColumn('bag_woonplaats_id');
            });
        }
    }
};
