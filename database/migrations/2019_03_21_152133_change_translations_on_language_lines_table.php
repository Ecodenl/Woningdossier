<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
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
        \App\Models\LanguageLine::where('group', 'general')->where('key', 'costs.cost-and-benefits.title')->update([
            'group' => 'insulated-glazing',
            'step_id' => DB::table('steps')->where('slug', 'insulated-glazing')->first()->id
        ]);
        \App\Models\LanguageLine::where('group', 'general')->where('key', 'costs.cost-and-benefits.help')->update([
            'group' => 'insulated-glazing',
            'step_id' => DB::table('steps')->where('slug', 'insulated-glazing')->first()->id
        ]);
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
