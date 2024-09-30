<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $interestTranslationQuery = DB::table('language_lines')->where('group', 'my-plan')->where('key',
            'columns.interest.title');
        if ($interestTranslationQuery->first() instanceof \stdClass) {
            $interestTranslationQuery->update(['text' => json_encode(['nl' => 'Inplannen'])]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $interestTranslationQuery = DB::table('language_lines')->where('group', 'my-plan')->where('key',
            'columns.interest.title');
        if ($interestTranslationQuery->first() instanceof \stdClass) {
            $interestTranslationQuery->update(['text' => json_encode(['nl' => 'Interesse'])]);
        }
    }
};
