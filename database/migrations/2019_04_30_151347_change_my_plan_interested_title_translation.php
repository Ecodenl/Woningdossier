<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeMyPlanInterestedTitleTranslation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $interestTranslationQuery = DB::table('language_lines')->where('group', 'my-plan')->where('key',
            'columns.interest.title');
        if ($interestTranslationQuery->first() instanceof stdClass) {
            $interestTranslationQuery->update(['text' => json_encode(['nl' => 'Inplannen'])]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $interestTranslationQuery = DB::table('language_lines')->where('group', 'my-plan')->where('key',
            'columns.interest.title');
        if ($interestTranslationQuery->first() instanceof stdClass) {
            $interestTranslationQuery->update(['text' => json_encode(['nl' => 'Interesse'])]);
        }
    }
}
