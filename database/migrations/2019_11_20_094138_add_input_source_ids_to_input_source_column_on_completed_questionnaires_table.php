<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $residentInputSource = DB::table('input_sources')->where('short', \App\Models\InputSource::RESIDENT_SHORT)->first();

        if ($residentInputSource instanceof \stdClass) {
            DB::table('completed_questionnaires')->update([
                'input_source_id' => $residentInputSource->id,
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
