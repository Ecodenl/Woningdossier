<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateMyPlanTranslationsOnLanguageLinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $keysToDelete = [
            'coach-comments.title',
            'description.help'
        ];
        $keysToAdd = [
            'trigger-modal-for-other-input-source.title',
            'modal-for-other-input-source.title',
            'modal-for-other-input-source.text'
        ];

        foreach ($keysToDelete as $keyToDelete) {
            DB::table('language_lines')
                ->where('group', 'my-plan')
                ->where('key', $keyToDelete)
                ->delete();
        }

        foreach ($keysToAdd as $keyToAdd) {
            DB::table('language_lines')->insert([
                'group' => 'my-plan',
                'key' => $keyToAdd,
                'text' => json_encode(['nl' => __('my-plan.'.$keyToAdd)])
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
        // no need to revert, we would have to revert the whole feature
    }
}
