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
                'indication-for-costs.performance.ideal' => ['nl' => 'Ideaal'],
                'indication-for-costs.performance.no-go' => ['nl' => 'Onrendabel'],
                'indication-for-costs.performance.possible' => ['nl' => 'Mogelijk'],
                'advice-text' => ['nl' => 'Voor het opwekken van uw huidige elektraverbruik heeft u in totaal ca. :number zonnepanelen in optimale oriëntatie nodig.'],
                'total-power' => ['nl' => 'Totale Wp vermogen van de installatie: :wp']
            ],
            'insulated-glazing' => [
                'paint-work.comments-paintwork' => [
                    'help' => ['nl' => 'Opmerking over schilderwerk helptext'],
                    'title' => ['nl' => 'Opmerking over schilderwerk'],
                ]
            ],
            'floor-insulation' => [
                'crawlspace.unknown-error.title' => ['nl' => 'Onbekend! Er is aanvullend onderzoek nodig. Om de vloer te kunnen isoleren moet eerst een kruipluik gemaakt worden.']
            ],
            'boiler' => [
                'already-efficient' => ['nl' => 'Het vervangen van de huidige ketel zal alleen een beperkte energiebesparing opleveren omdat u al een HR ketel hebt.']
            ],
            'general' => [
                'need-advice-from-specialist-alert' => ['nl' => 'Hoeveel u met deze maatregel kunt besparen hangt ervan wat de isolatiewaarde van de huidige isolatielaag is. Voor het uitrekenen van de daadwerkelijke besparing bij het na- isoleren van een reeds geïsoleerde gevel/vloer/dak is aanvullend en gespecialiseerd advies nodig.']
            ],
        ];

        foreach ($languageLinesData as $group => $languageLines) {
            // some stuff to deal with things.
	        $stepId = null;
            if ($group == 'boiler') {
                $step = DB::table('steps')->where('slug', 'high-efficiency-boiler')->first();
                if ($step instanceof stdClass){
                	$stepId = $step->id;
                }
            } elseif (DB::table('steps')->where('slug', $group)->first() instanceof stdClass) {
                $stepId = DB::table('steps')->where('slug', $group)->first()->id;
            }

            if (!is_null($stepId)) {
	            foreach ( $languageLines as $key => $translation ) {
		            if ( count( $translation ) > 1 ) {
			            $fullHelpKey  = $key . '.help';
			            $fullTitleKey = $key . '.title';

			            // check if the title and help key does not exists.
			            if ( ! DB::table( 'language_lines' )->where( 'group',
						            $group )->where( 'key',
						            $fullHelpKey )->first() instanceof stdClass && ! DB::table( 'language_lines' )->where( 'group',
						            $group )->where( 'key',
						            $fullTitleKey )->first() instanceof stdClass ) {
				            $helpLanguageLine = App\Models\LanguageLine::create( [
					            'group'   => $group,
					            'key'     => $fullHelpKey,
					            'text'    => $translation['help'],
					            'step_id' => $stepId
				            ] );
				            App\Models\LanguageLine::create( [
					            'group'                 => $group,
					            'key'                   => $fullTitleKey,
					            'text'                  => $translation['title'],
					            'help_language_line_id' => $helpLanguageLine->id,
					            'step_id'               => $stepId
				            ] );
			            } else {
				            dump( 'Key ' . $key . ' already exists in group ' . $group );
			            }
		            } else {
			            if ( ! DB::table( 'language_lines' )->where( 'group',
					            $group )->where( 'key',
					            $key )->first() instanceof stdClass ) {
				            App\Models\LanguageLine::create( [
					            'group'   => $group,
					            'key'     => $key,
					            'text'    => $translation,
					            'step_id' => $stepId
				            ] );
			            } else {
				            dump( 'Key ' . $key . ' already exists in group ' . $group );
			            }
		            }
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
        //
    }
}
