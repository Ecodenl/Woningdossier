<?php

use Illuminate\Database\Migrations\Migration;

class RemoveInsulatedGlazingIndicationForCostsGasTranslationFromLanguageLines extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $help = DB::table('language_lines')
                  ->where('group', 'insulated-glazing')
                  ->where('key', 'indication-for-costs.gas-savings.help')->first();

        $title = DB::table('language_lines')
                   ->where('group', 'insulated-glazing')
                   ->where('key', 'indication-for-costs.gas-savings.title')->first();

        if ($title instanceof stdClass && $help instanceof stdClass) {
            DB::table('language_lines')
              ->where('group', 'insulated-glazing')
              ->where('key', 'indication-for-costs.gas-savings.title')
              ->delete();

            DB::table('language_lines')
              ->where('group', 'insulated-glazing')
              ->where('key', 'indication-for-costs.gas-savings.help')
              ->delete();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $step = DB::table('steps')->where('slug', 'insulated-glazing')->first();
        if ($step instanceof stdClass) {
            DB::table('language_lines')
              ->insert([
                  'group'   => 'insulated-glazing',
                  'step_id' => $step->id,
                  'key'     => 'indication-for-costs.gas-savings.help',
                  'text'    => json_encode(['nl' => 'De besparing wordt berekend op basis van de door u ingevoerde woningkenmerken (hoeveelheden, isolatiewaarde, gebruikersgedrag). Hier worden alle besparingen van boven genoemde maatregelen bij elkaar opgeteld']),
              ]);

            $helpId = DB::table('language_lines')->where('key', 'indication-for-costs.gas-savings.help')
                        ->where('group', 'insulated-glazing')->first()->id;

            DB::table('language_lines')
              ->insert([
                  'step_id'               => $step->id,
                  'group'                 => 'insulated-glazing',
                  'key'                   => 'indication-for-costs.gas-savings.title',
                  'text'                  => json_encode(['nl' => 'Gasbesparing']),
                  'help_language_line_id' => $helpId,
              ]);
        }
    }
}
