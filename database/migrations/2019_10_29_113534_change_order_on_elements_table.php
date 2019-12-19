<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeOrderOnElementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('elements')->where('short', 'crack-sealing')->update(['order' => 2]);
        DB::table('elements')->where('short', 'wall-insulation')->update(['order' => 3]);
        DB::table('elements')->where('short', 'floor-insulation')->update(['order' => 4]);
        DB::table('elements')->where('short', 'roof-insulation')->update(['order' => 5]);
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('elements')->where('short', 'crack-sealing')->update(['order' => 5]);
        DB::table('elements')->where('short', 'wall-insulation')->update(['order' => 2]);
        DB::table('elements')->where('short', 'floor-insulation')->update(['order' => 3]);
        DB::table('elements')->where('short', 'roof-insulation')->update(['order' => 4]);
    }
}
