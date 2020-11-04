<?php

use Illuminate\Database\Migrations\Migration;

class ChangeTranslationsOnLanguageLinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // no fancy arrays..
        $step = DB::table('steps')->where('slug', 'insulated-glazing')->first();
        if ($step instanceof stdClass) {
            \App\Models\LanguageLine::where('group', 'general')->where('key',
                'costs.cost-and-benefits.title')->update([
                'group' => 'insulated-glazing',
                'step_id' => $step->id,
            ]);
        }

        $step = DB::table('steps')->where('slug', 'insulated-glazing')->first();
        if ($step instanceof stdClass) {
            \App\Models\LanguageLine::where('group', 'general')->where('key',
                'costs.cost-and-benefits.help')->update([
                'group' => 'insulated-glazing',
                'step_id' => $step->id,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
