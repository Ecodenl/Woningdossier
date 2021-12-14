<?php

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
        // TODO: Should this go? We have the LegacyCleanup/DeleteLanguageLines now

        $groupsToDelete = [
            'cooperation/mail/account-associated-with-cooperation',
            'cooperation/mail/account-created',
            'cooperation/mail/changed-email',
            'cooperation/mail/confirm-account',
            'cooperation/mail/reset-password',
            'cooperation/mail/unread-message-count',
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
    }
}
