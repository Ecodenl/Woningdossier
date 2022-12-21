<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEditableColumnsToMeasureApplicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasColumn('measure_applications', 'cost_range')) {
            Schema::table('measure_applications', function (Blueprint $table) {
                $table->json('cost_range')->nullable()->after('application');
                $table->decimal('savings_money')->nullable()->after('cost_range');
                $table->boolean('has_calculations')->default(true)->after('step_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('measure_applications', 'cost_range')) {
            Schema::table('measure_applications', function (Blueprint $table) {
                $table->dropColumn('cost_range', 'savings_money', 'has_calculations');
            });
        }
    }
}
