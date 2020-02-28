<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveOldMyPlanWarningsFromLanguageLinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('language_lines')
            ->where('group', 'my-plan')
            ->where('key', 'warnings.roof-insulation.check-order.title')
            ->orWhere('key', 'warnings.roof-insulation.planned-year.title')
            ->delete();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
