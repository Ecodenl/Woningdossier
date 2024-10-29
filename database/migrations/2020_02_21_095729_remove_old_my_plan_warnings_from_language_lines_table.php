<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('language_lines')
            ->where('group', 'my-plan')
            ->where('key', 'warnings.roof-insulation.check-order.title')
            ->orWhere('key', 'warnings.roof-insulation.planned-year.title')
            ->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
