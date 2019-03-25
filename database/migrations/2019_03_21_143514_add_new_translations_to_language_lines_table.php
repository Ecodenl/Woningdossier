<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewTranslationsToLanguageLinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @note we use the LanguageLine model so the cache get flushed.
     *
     * @return void
     */
    public function up()
    {
        $languageLinesData = [
            'solar-panels' => [
                'indication-for-costs.performance.ideal' => ['nl' => __('woningdossier.cooperation.tool.solar-panels.indication-for-costs.performance.ideal')],
                'indication-for-costs.performance.no-go' => ['nl' => __('woningdossier.cooperation.tool.solar-panels.indication-for-costs.performance.no-go')],
                'indication-for-costs.performance.possible' => ['nl' => __('woningdossier.cooperation.tool.solar-panels.indication-for-costs.performance.possible')],
                'advice-text' => ['nl' => __('woningdossier.cooperation.tool.solar-panels.advice-text')],
                'total-power' => ['nl' => __('woningdossier.cooperation.tool.solar-panels.total-power')]
            ],
            'insulated-glazing' => [
                'paint-work.comments-paintwork' => [
                    'help' => ['nl' => 'Opmerking over schilderwerk helptext'],
                    'title' => ['nl' => 'Opmerking over schilderwerk'],
                ]
            ]

        ];

        foreach ($languageLinesData as $group => $languageLines) {
            $stepId = DB::table('steps')->where('slug', $group)->first()->id;
            foreach ($languageLines as $key => $translation) {
                if (count($translation) > 1) {
                    $fullHelpKey = $key.'.help';
                    $fullTitleKey = $key.'.title';

                    // check if the title and help key does not exists.
                    if (!DB::table('language_lines')->where('group', $group)->where('key', $fullHelpKey)->first() instanceof stdClass && !DB::table('language_lines')->where('group', $group)->where('key', $fullTitleKey)->first() instanceof stdClass) {
                        $helpLanguageLine = App\Models\LanguageLine::create([
                            'group' => $group,
                            'key' => $fullHelpKey,
                            'text' => $translation['help'],
                            'step_id' => $stepId
                        ]);
                        App\Models\LanguageLine::create([
                            'group' => $group,
                            'key' => $fullTitleKey,
                            'text' => $translation['title'],
                            'help_language_line_id' => $helpLanguageLine->id,
                            'step_id' => $stepId
                        ]);
                    } else {
                        dump('De key ' . $key . ' bestaat al in de group ' . $group . ' wat vervelend!');
                    }
                } else {
                    if (!DB::table('language_lines')->where('group', $group)->where('key', $key)->first() instanceof stdClass) {
                        App\Models\LanguageLine::create([
                            'group' => $group,
                            'key' => $key,
                            'text' => $translation,
                            'step_id' => $stepId
                        ]);
                    } else {
                        dump('De key ' . $key . ' bestaat al in de group ' . $group . ' wat vervelend!');
                    }
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
