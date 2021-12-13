<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMeasureInfoColumnToMeasureApplicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasColumn('measure_applications', 'measure_info')) {
            Schema::table('measure_applications', function (Blueprint $table) {
                $table->json('measure_info')->after('measure_name')->nullable();
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
        if (Schema::hasColumn('measure_applications', 'measure_info')) {
            Schema::table('measure_applications', function (Blueprint $table) {
                $table->dropColumn('measure_info');
            });
        }
    }
}
