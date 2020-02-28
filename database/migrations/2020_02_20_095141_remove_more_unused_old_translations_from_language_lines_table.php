<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveMoreUnusedOldTranslationsFromLanguageLinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('language_lines')
            ->where('group', 'general')
            ->where('key', 'costs.indicative-costs-insulation.title')
            ->orWhere('key', 'costs.indicative-costs-insulation.help')
            ->orWhere('key', 'costs.gas.title')
            ->orWhere('key', 'costs.gas.help')
            ->orWhere('key', 'costs.co2.title')
            ->orWhere('key', 'costs.co2.help')
            ->delete();

        DB::table('language_lines')
            ->where('group', 'insulated-glazing')
            ->where('key', 'indication-for-costs.co2-savings.title')
            ->orWhere('key', 'indication-for-costs.co2-savings.help')
            ->delete();

        DB::table('language_lines')
            ->where('group', 'roof-insulation')
            ->where('key', 'flat.costs.co2.title')
            ->orWhere('key', 'flat.costs.co2.help')
            ->orWhere('key', 'flat.costs.gas.title')
            ->orWhere('key', 'flat.costs.gas.help')

            ->orWhere('key', 'pitched.costs.co2.title')
            ->orWhere('key', 'pitched.costs.co2.help')
            ->orWhere('key', 'pitched.costs.gas.title')
            ->orWhere('key', 'pitched.costs.gas.help')
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
