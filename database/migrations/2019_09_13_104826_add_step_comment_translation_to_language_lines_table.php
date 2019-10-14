<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStepCommentTranslationToLanguageLinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $steps = DB::table('steps')->get();
        foreach ($steps as $step) {
            DB::table('language_lines')->insert([
                'group' => $step->slug,
                'key' => 'comment.title',
                'text' => json_encode([
                    'nl' => 'Toelichting op '.DB::table('translations')->where('key', $step->name)->where('language', 'nl')->first()->translation,
                ]),
                'step_id' => $step->id
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
        // sike.
    }
}
