<?php

use Illuminate\Database\Migrations\Migration;

class SeedUpdateGlazingMeasureNames extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $translationUpdates = [
            'nl' => [
                'glass-in-lead' => 'Glas in lood vervangen',
                'hrpp-glass-only' => 'Plaatsen van HR++ glas (alleen het glas)',
                'hrpp-glass-frames' => 'Plaatsen van HR++ glas (inclusief kozijn)',
                'hr3p-frames' => 'Plaatsen van drievoudige HR beglazing (inclusief kozijn)',
            ],
        ];

        $measureShorts = [];
        foreach ($translationUpdates as $lang => $updates) {
            $measureShorts = array_merge($measureShorts, array_keys($updates));
        }
        $measureShorts = array_unique($measureShorts);

        $measures = \App\Models\MeasureApplication::whereIn('short', $measureShorts)->get();

        /** @var \App\Models\MeasureApplication $measureApplication */
        foreach ($measures as $measureApplication) {
            foreach ($translationUpdates as $lang => $updates) {
                if (array_key_exists($measureApplication->short, $updates)) {
                    // update language for this key
                    $measureApplication->updateTranslation('measure_name', $updates[$measureApplication->short], $lang);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Not possible
    }
}
