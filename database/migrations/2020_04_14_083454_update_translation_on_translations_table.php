<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $step = DB::table('steps')->where('short', 'ventilation')->first();

        if ($step instanceof \stdClass) {
            DB::table('translations')
                ->where('key', $step->name)
                ->where('language', 'nl')
                ->update([
                    'translation' => 'Ventilatie',
                ]);

            DB::table('translations')
                ->where('key', $step->name)
                ->where('language', 'en')
                ->update([
                    'translation' => 'Ventilation',
                ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $step = DB::table('steps')->where('short', 'ventilation')->first();

        if ($step instanceof \stdClass) {
            DB::table('translations')
                ->where('key', $step->name)
                ->where('language', 'nl')
                ->update([
                    'translation' => 'Ventilatie informatie',
                ]);

            DB::table('translations')
                ->where('key', $step->name)
                ->where('language', 'en')
                ->update([
                    'translation' => 'Ventilation information',
                ]);
        }
    }
};
