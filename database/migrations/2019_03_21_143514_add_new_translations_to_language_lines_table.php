<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewTranslationsToLanguageLinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $languageLinesData = [
            'solar-panels' => [
                'indication-for-costs.performance.ideal' => ['nl' => __('woningdossier.cooperation.tool.solar-panels.indication-for-costs.performance.ideal')],
                'indication-for-costs.performance.no-go' => ['nl' =>__('woningdossier.cooperation.tool.solar-panels.indication-for-costs.performance.no-go')],
                'indication-for-costs.performance.possible' => ['nl' =>__('woningdossier.cooperation.tool.solar-panels.indication-for-costs.performance.possible')],
                'advice-text' => ['nl' => __('woningdossier.cooperation.tool.solar-panels.advice-text')],
                'total-power' => ['nl' => __('woningdossier.cooperation.tool.solar-panels.total-power')]
            ],
            'insulated-glazing' => [
                'insulated-glazing.paint-work.comments-paintwork.title' => [
                    'title' => ['nl' => 'Opmerking over schilderwerk'],
                    'help' => ['nl' => 'Opmerking over schilderwerk helptext']
                ]
            ]

        ];

        foreach ($languageLinesData as $group => $languageLines) {
            foreach ($languageLines as $key => $translation) {
                dump($translation);
                if (!DB::table('language_lines')->where('group', $group)->where('key', $key)->first() instanceof stdClass) {
                    if (count($translation) > 1) {
                        // todo
                        foreach ($translation as $question);
                    } else {
                        // use the model so the cache get flushed.
                        App\Models\LanguageLine::create([
                            'group' => $group,
                            'key' => $key,
                            'text' => $translation,
                            'step_id' => DB::table('steps')->where('slug', $group)->first()->id
                        ]);
                    }
                } else {
                    dump('De key '.$key.' bestaat al in de group '.$group.' wat vervelend!');
                }
            }
        }

        dd('edf');

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
