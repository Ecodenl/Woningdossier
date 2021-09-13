<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddConfigurationsToVariousTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasColumn('measure_applications', 'configurations')) {
            Schema::table('measure_applications', function (Blueprint $table) {
                $table->json('configurations')->after('step_id')->nullable();
            });
        }
        if (! Schema::hasColumn('element_values', 'configurations')) {
            Schema::table('element_values', function (Blueprint $table) {
                $table->json('configurations')->after('order')->nullable();
            });
        }
        if (! Schema::hasColumn('service_values', 'configurations')) {
            Schema::table('service_values', function (Blueprint $table) {
                $table->json('configurations')->after('is_default')->nullable();
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
        if (Schema::hasColumn('measure_applications', 'configurations')) {
            Schema::table('measure_applications', function (Blueprint $table) {
                $table->dropColumn('configurations');
            });
        }
        if (Schema::hasColumn('element_values', 'configurations')) {
            Schema::table('element_values', function (Blueprint $table) {
                $table->dropColumn('configurations');
            });
        }
        if (Schema::hasColumn('service_values', 'configurations')) {
            Schema::table('service_values', function (Blueprint $table) {
                $table->dropColumn('configurations');
            });
        }
    }
}
