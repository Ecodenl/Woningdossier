<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCountryColumnToPvPanelLocationFactorsTable extends Migration
{
    public function up(): void
    {
        //Schema::whenTableDoesntHaveColumn('pv_panel_location_factors', 'country', function (BluePrint $table) {
        //    $table->string('country')->default(\App\Enums\Country::COUNTRY_NL)->after('location');
        //});
        if (! Schema::hasColumn('pv_panel_location_factors', 'country')) {
            Schema::table('pv_panel_location_factors', function (BluePrint $table) {
                $table->string('country')->default(\App\Enums\Country::COUNTRY_NL)->after('location');
            });
        }
    }

    public function down(): void
    {
        //Schema::whenTableHasColumn('pv_panel_location_factors', 'country', function (BluePrint $table) {
        //    $table->dropColumn('country');
        //});
        if (Schema::hasColumn('pv_panel_location_factors', 'country')) {
            Schema::table('pv_panel_location_factors', function (BluePrint $table) {
                $table->dropColumn('country');
            });
        }
    }
}
