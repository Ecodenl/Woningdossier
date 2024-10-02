<?php

use App\Models\LanguageLine;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $stepId = null;
        if (DB::table('steps')->where('slug', 'wall-insulation')->first() instanceof \stdClass) {
            $stepId = DB::table('steps')->where('slug', 'wall-insulation')->first()->id;
        }

        LanguageLine::create([
                'group' => 'wall-insulation',
                'key' => 'wall-insulation-research',
                'text' => ['nl' => 'Er is nader onderzoek nodig hoe de gevel het beste geïsoleerd kan worden'],
                'step_id' => $stepId,
            ]);

        LanguageLine::create([
                'group' => 'wall-insulation',
                'key' => 'facade-wall-insulation',
                'text' => ['nl' => 'Binnengevelisolatie'],
                'step_id' => $stepId,
            ]);

        LanguageLine::create([
                'group' => 'wall-insulation',
                'key' => 'cavity-wall-insulation',
                'text' => ['nl' => 'Spouwmuurisolatie'],
                'step_id' => $stepId,
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
