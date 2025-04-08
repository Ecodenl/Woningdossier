<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCountryColumnToCooperationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //Schema::whenTableDoesntHaveColumn('cooperations', 'country', function (BluePrint $table) {
        //    $table->string('country')->default(\App\Enums\Country::COUNTRY_NL)->after('slug');
        //});
        if (! Schema::hasColumn('cooperations', 'country')) {
            Schema::table('cooperations', function (BluePrint $table) {
                $table->string('country')->default(\App\Enums\Country::COUNTRY_NL)->after('slug');
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
        //Schema::whenTableHasColumn('cooperations', 'country', function (BluePrint $table) {
        //    $table->dropColumn('country');
        //});
        if (Schema::hasColumn('cooperations', 'country')) {
            Schema::table('cooperations', function (BluePrint $table) {
                $table->dropColumn('country');
            });
        }
    }
}
