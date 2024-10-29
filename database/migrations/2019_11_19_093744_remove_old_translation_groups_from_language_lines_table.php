<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('language_lines')->whereIn('group', ['general-data', 'ventilation-information', 'building-detail'])->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
