<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBagWoonplaatsIdToBuildingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if ( ! Schema::hasColumn('buildings', 'bag_woonplaats_id')) {
            Schema::table('buildings', function (Blueprint $table) {
                $table->string('bag_woonplaats_id')->after('bag_addressid')->nullable()->default(null);
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
        if (Schema::hasColumn('buildings', 'bag_woonplaats_id')) {
            Schema::table('buildings', function (Blueprint $table) {
                $table->dropColumn('bag_woonplaats_id');
            });
        }
    }
}
