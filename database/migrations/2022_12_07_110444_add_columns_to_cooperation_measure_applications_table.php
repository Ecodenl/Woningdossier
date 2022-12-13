<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToCooperationMeasureApplicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasColumn('cooperation_measure_applications', 'is_deletable')) {
            Schema::table('cooperation_measure_applications', function (Blueprint $table) {
                $table->boolean('is_extensive_measure')->default(false)->after('extra');
                $table->boolean('is_deletable')->default(false)->after('is_extensive_measure');
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
        if (Schema::hasColumn('cooperation_measure_applications', 'is_deletable')) {
            Schema::table('cooperation_measure_applications', function (Blueprint $table) {
                $table->dropColumn('is_extensive_measure', 'is_deletable');
            });
        }
    }
}
