<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $usageStep = DB::table('steps')->where('short', 'usage')->first();
        $generalDataStep = DB::table('steps')->where('short', 'general-data')->first();

        if ($usageStep instanceof \stdClass) {
            DB::table('step_comments')
                ->where('step_id', $generalDataStep->id)
                ->update([
                    'step_id' => $usageStep->id,
                ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
