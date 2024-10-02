<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $steps = DB::table('steps')->get();
        foreach ($steps as $step) {
            DB::table('language_lines')->insert([
                'group' => $step->slug,
                'key' => 'comment.title',
                'text' => json_encode([
                    'nl' => 'Toelichting op '.DB::table('translations')->where('key', $step->name)->where('language', 'nl')->first()->translation,
                ]),
                'step_id' => $step->id,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // sike.
    }
};
