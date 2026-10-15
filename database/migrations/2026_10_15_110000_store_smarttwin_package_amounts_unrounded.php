<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // SmartTwin's amounts are never rounded (Patrick, 15 October 2026). A decimal(10,2) rounded
        // them to cents on the way in; SmartTwin sends doubles (a subsidy of 6853.749263548063), and
        // a double keeps them as sent.
        Schema::table('smarttwin_packages', function (Blueprint $table) {
            $table->double('total_cost_with_tax')->nullable()->change();
            $table->double('energy_cost_savings_per_month')->nullable()->change();
            $table->double('total_subsidy')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('smarttwin_packages', function (Blueprint $table) {
            $table->decimal('total_cost_with_tax', 10, 2)->nullable()->change();
            $table->decimal('energy_cost_savings_per_month', 10, 2)->nullable()->change();
            $table->decimal('total_subsidy', 10, 2)->nullable()->change();
        });
    }
};
