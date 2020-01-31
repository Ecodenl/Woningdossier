<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveOldTranslationsFromLanguageLinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // delete the old general translation to keep it a bit clean
        DB::table('language_lines')
            ->where('group', 'general')
            ->where('key', 'interested-in-improvement.help')
            ->orWhere('key', 'interested-in-improvement.title')
            ->orWhere('key', 'specific-situation.title')
            ->orWhere('key', 'specific-situation.help')
            ->orWhere('key', 'costs.indicative-costs.title')
            ->orWhere('key', 'costs.indicative-costs.help')
            ->orWhere('key', 'costs.savings-in-euro.title')
            ->orWhere('key', 'costs.savings-in-euro.help')
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
