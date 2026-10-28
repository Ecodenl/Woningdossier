<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cooperations', function (Blueprint $table) {
            // Whether this cooperation takes part in SmartTwin. On by default, so every existing
            // cooperation behaves as it did under the installation wide switch; the ones that do not
            // take part are switched off by a super admin. SMARTTWIN_ENABLED stays the switch above
            // this one: with it off, no cooperation reaches SmartTwin.
            $table->boolean('smarttwin_enabled')->default(true)->after('econobis_api_key');
        });
    }

    public function down(): void
    {
        Schema::table('cooperations', function (Blueprint $table) {
            $table->dropColumn('smarttwin_enabled');
        });
    }
};
