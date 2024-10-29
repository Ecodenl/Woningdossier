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
        if (! Schema::hasColumn('cooperations', 'econobis_api_key')) {
            Schema::table('cooperations', function (Blueprint $table) {
                $table->longText('econobis_api_key')->nullable()->after('econobis_wildcard');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('cooperations', 'econobis_api_key')) {
            Schema::table('cooperations', function (Blueprint $table) {
                $table->dropColumn('econobis_api_key');
            });
        }
    }
};
