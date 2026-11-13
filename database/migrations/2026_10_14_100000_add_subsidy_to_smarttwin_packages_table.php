<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // SmartTwin gives the subsidy for the package as a whole, per scheme, not per measure.
        Schema::table('smarttwin_packages', function (Blueprint $table) {
            $table->decimal('total_subsidy', 10, 2)->nullable()->after('energy_cost_savings_per_month');
            $table->json('subsidies')->nullable()->after('total_subsidy');
        });
    }

    public function down(): void
    {
        Schema::table('smarttwin_packages', function (Blueprint $table) {
            $table->dropColumn(['total_subsidy', 'subsidies']);
        });
    }
};
