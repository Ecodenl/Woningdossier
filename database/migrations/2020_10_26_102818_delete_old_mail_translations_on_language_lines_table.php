<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DeleteOldMailTranslationsOnLanguageLinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $groupsToDelete = [
            'cooperation/mail/account-associated-with-cooperation',
            'cooperation/mail/changed-email'
        ];

        DB::table('language_lines')
            ->whereIn('group', $groupsToDelete)
            ->delete();

        // we need to clear the cache, otherwise we would just import the same translations again.
        Artisan::call('cache:clear');

        Artisan::call('translations:import', [
            '--only-groups' => implode(',', $groupsToDelete),
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
