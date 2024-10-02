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
        if (!Schema::hasColumn('mappings', 'type')) {
            Schema::table('mappings', function (Blueprint $table) {
                $table->string('type')->nullable()->default(null)->after('id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('mappings', 'type')) {
            Schema::table('mappings', function (Blueprint $table) {
                $table->dropColumn('type');
            });
        }
    }
};
