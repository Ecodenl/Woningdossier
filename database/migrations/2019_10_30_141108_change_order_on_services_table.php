<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeOrderOnServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('services')->where('short', 'hr-boiler')->update(['order' => 0]);
        DB::table('services')->where('short', 'boiler')->update(['order' => 1]);
        DB::table('services')->where('short', 'heat-pump')->update(['order' => 2]);
        DB::table('services')->where('short', 'total-sun-panels')->update(['order' => 3]);
        DB::table('services')->where('short', 'sun-boiler')->update(['order' => 4]);
        DB::table('services')->where('short', 'house-ventilation')->update(['order' => 5]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('services')->where('short', 'hr-boiler')->update(['order' => 0]);
        DB::table('services')->where('short', 'boiler')->update(['order' => 0]);
        DB::table('services')->where('short', 'heat-pump')->update(['order' => 0]);
        DB::table('services')->where('short', 'total-sun-panels')->update(['order' => 0]);
        DB::table('services')->where('short', 'sun-boiler')->update(['order' => 0]);
        DB::table('services')->where('short', 'house-ventilation')->update(['order' => 0]);
    }
}
