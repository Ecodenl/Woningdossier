<?php

use Illuminate\Database\Seeder;

class ToolCalculationResultsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $calculationResults = [
            [
                'name' => [
                    'nl' => 'Huidig gasverbruik',
                ],
                'short' => 'hr-boiler.amount_gas',
                'unit_of_measure' => 'm<sup>3</sup> / jaar',
            ],
            [
                'name' => [
                    'nl' => 'Huidig elektragebruik',
                ],
                'short' => 'hr-boiler.amount_electricity',
                'unit_of_measure' => 'kWh / jaar',
            ],
            [
                'name' => [
                    'nl' => 'Gasbesparing',
                ],
                'short' => 'hr-boiler.savings_gas',
                'unit_of_measure' => 'm<sup>3</sup> / jaar',
            ],
            [
                'name' => [
                    'nl' => 'CO2 Besparing',
                ],
                'short' => 'hr-boiler.savings_co2',
                'unit_of_measure' => 'kg / jaar',
            ],
            [
                'name' => [
                    'nl' => 'Besparing in €',
                ],
                'short' => 'hr-boiler.savings_money',
                'unit_of_measure' => '<i class="icon-sm icon-moneybag mr-1"></i> / jaar',
            ],
            [
                'name' => [
                    'nl' => 'Indicatie vervangingsmoment CV ketel',
                ],
                'short' => 'hr-boiler.replace_year',
                'unit_of_measure' => '<i class="icon-sm icon-timer"></i>',
            ],
            [
                'name' => [
                    'nl' => 'Indicatieve kosten',
                ],
                'short' => 'hr-boiler.cost_indication',
                'unit_of_measure' => '<i class="icon-sm icon-moneybag"></i>',
            ],
            [
                'name' => [
                    'nl' => 'Vergelijkbare rente',
                ],
                'short' => 'hr-boiler.interest_comparable',
                'unit_of_measure' => '% / jaar',
            ],
            [
                'name' => [
                    'nl' => 'Benodigd indicatief vermogen van de warmtepomp',
                ],
                'short' => 'heat-pump.advised_system.required_power',
                'unit_of_measure' => 'kW',
            ],
            [
                'name' => [
                    'nl' => 'Aandeel van de warmtepomp aan de verwarming',
                ],
                'short' => 'heat-pump.advised_system.share_heating',
                'unit_of_measure' => '%',
            ],
            [
                'name' => [
                    'nl' => 'Aandeel van de warmtepomp aan warm tapwater',
                ],
                'short' => 'heat-pump.advised_system.share_tap_water',
                'unit_of_measure' => '%',
            ],
            [
                'name' => [
                    'nl' => 'SCOP van de warmtepomp voor verwarming',
                ],
                'help_text' => [
                    'nl' => 'SCOP staat voor "Seasonal Coefficient of Performance". De SCOP is een gemiddelde COP over een jaar, waarbij de seizoenen in een bepaalde regio zijn meegewogen.',
                ],
                'short' => 'heat-pump.advised_system.scop_heating',
                'unit_of_measure' => null,
            ],
            [
                'name' => [
                    'nl' => 'SCOP van de warmtepomp voor warm tapwater',
                ],
                'help_text' => [
                    'nl' => 'SCOP staat voor "Seasonal Coefficient of Performance". De SCOP is een gemiddelde COP over een jaar, waarbij de seizoenen in een bepaalde regio zijn meegewogen.',
                ],
                'short' => 'heat-pump.advised_system.scop_tap_water',
                'unit_of_measure' => null,
            ],
            [
                'name' => [
                    'nl' => 'Huidig gasverbruik',
                ],
                'short' => 'heat-pump.amount_gas',
                'unit_of_measure' => 'm<sup>3</sup> / jaar',
            ],
            [
                'name' => [
                    'nl' => 'Huidig elektragebruik',
                ],
                'short' => 'heat-pump.amount_electricity',
                'unit_of_measure' => 'kWh / jaar',
            ],
            [
                'name' => [
                    'nl' => 'Gasbesparing',
                ],
                'short' => 'heat-pump.savings_gas',
                'unit_of_measure' => 'm<sup>3</sup> / jaar',
            ],
            [
                'name' => [
                    'nl' => 'CO2 Besparing',
                ],
                'short' => 'heat-pump.savings_co2',
                'unit_of_measure' => 'kg / jaar',
            ],
            [
                'name' => [
                    'nl' => 'Besparing in €',
                ],
                'short' => 'heat-pump.savings_money',
                'unit_of_measure' => '<i class="icon-sm icon-moneybag mr-1"></i> / jaar',
            ],
            [
                'name' => [
                    'nl' => 'Indicatie vervangingsmoment CV ketel',
                ],
                'short' => 'heat-pump.replace_year',
                'unit_of_measure' => '<i class="icon-sm icon-timer"></i>',
            ],
            [
                'name' => [
                    'nl' => 'Indicatieve kosten',
                ],
                'short' => 'heat-pump.cost_indication',
                'unit_of_measure' => '<i class="icon-sm icon-moneybag"></i>',
            ],
            [
                'name' => [
                    'nl' => 'Vergelijkbare rente',
                ],
                'short' => 'heat-pump.interest_comparable',
                'unit_of_measure' => '% / jaar',
            ],
            [
                'name' => [
                    'nl' => 'Gebruik warm tapwater',
                ],
                'help_text' => [
                    'nl' => 'Het gebruik voor warm tapwater is afhankelijk van het aantal gebruikers en het comfortniveau.',
                ],
                'short' => 'sun-boiler.usage-warm-tap-water',
                'unit_of_measure' => 'liter / jaar',
            ],
            [
                'name' => [
                    'nl' => 'Bijbehorend gasverbruik',
                ],
                'help_text' => [
                    'nl' => 'Hier wordt een inschatting gegeven van het jaarlijkse gasverbruik voor warm tapwater.',
                ],
                'short' => 'sun-boiler.usage-gas',
                'unit_of_measure' => 'm<sup>3</sup> / jaar',
            ],
            [
                'name' => [
                    'nl' => 'Grootte zonneboiler',
                ],
                'help_text' => [
                    'nl' => 'Op basis van de verbruikscijfers wordt hier een inschatting gegeven hoe groot het buffervat zou moeten zijn.',
                ],
                'short' => 'sun-boiler.size',
                'unit_of_measure' => 'liter',
            ],
            [
                'name' => [
                    'nl' => 'Grootte collector',
                ],
                'help_text' => [
                    'nl' => 'Op basis van de verbruikscijfers wordt hier een inschatting gegeven hoe groot de collector zou moeten zijn.',
                ],
                'short' => 'sun-boiler.collector-size',
                'unit_of_measure' => 'm<sup>2</sup>',
            ],
            [
                'name' => [
                    'nl' => 'Warmteproductie per jaar',
                ],
                'help_text' => [
                    'nl' => ' Hier kunt u zien hoeveel warmte de zonneboiler per jaar op kan wekken.',
                ],
                'short' => 'sun-boiler.heat-production',
                'unit_of_measure' => 'kWh / jaar',
            ],
            [
                'name' => [
                    'nl' => 'Aandeel van de zonneboiler aan het totaalverbruik voor warm water',
                ],
                'help_text' => [
                    'nl' => ' Hier kunt u zien hoeveel % van uw huidig energie voor warm tapwater u met dit zonneboiler systeem kunt opwekken.',
                ],
                'short' => 'sun-boiler.warm-tap-water-share',
                'unit_of_measure' => '%',
            ],
            [
                'name' => [
                    'nl' => 'Huidig gasverbruik',
                ],
                'short' => 'sun-boiler.amount_gas',
                'unit_of_measure' => 'm<sup>3</sup> / jaar',
            ],
            [
                'name' => [
                    'nl' => 'Huidig elektragebruik',
                ],
                'short' => 'sun-boiler.amount_electricity',
                'unit_of_measure' => 'kWh / jaar',
            ],
            [
                'name' => [
                    'nl' => 'Gasbesparing',
                ],
                'help_text' => [
                    'nl' => 'De besparing wordt berekend op basis van de door u ingevoerde woningkenmerken (hoeveelheden, isolatiewaarde, gebruikersgedrag).',
                ],
                'short' => 'sun-boiler.savings_gas',
                'unit_of_measure' => 'm<sup>3</sup> / jaar',
            ],
            [
                'name' => [
                    'nl' => 'CO2 Besparing',
                ],
                'help_text' => [
                    'nl' => 'Gerekend wordt met 1,88 kg/m<sup>3</sup> gas (bron: Milieucentraal)',
                ],
                'short' => 'sun-boiler.savings_co2',
                'unit_of_measure' => 'kg / jaar',
            ],
            [
                'name' => [
                    'nl' => 'Besparing in €',
                ],
                'help_text' => [
                    'nl' => 'Indicatieve besparing in € per jaar. De gebruikte energietarieven voor gas en elektra worden jaarlijks aan de marktomstandigheden aangepast.',
                ],
                'short' => 'sun-boiler.savings_money',
                'unit_of_measure' => '<i class="icon-sm icon-moneybag mr-1"></i> / jaar',
            ],
            [
                'name' => [
                    'nl' => 'Indicatieve kosten',
                ],
                'help_text' => [
                    'nl' => ' Hier kunt u zien wat de indicatieve kosten voor deze maatregel zijn.',
                ],
                'short' => 'sun-boiler.cost_indication',
                'unit_of_measure' => '<i class="icon-sm icon-moneybag"></i>',
            ],
            [
                'name' => [
                    'nl' => 'Vergelijkbare rente',
                ],
                'help_text' => [
                    'nl' => 'Meer informatie over de vergelijkbare rente kunt u vinden bij Milieucentraal: <a title="Link Milieucentraal" href="https://www.milieucentraal.nl/energie-besparen/energiezuinig-huis/financiering-energie-besparen/rendement-energiebesparing/" target="_blank" rel="noopener">https://www.milieucentraal.nl/energie-besparen/energiezuinig-huis/financiering-energie-besparen/rendement-energiebesparing/</a>',
                ],
                'short' => 'sun-boiler.interest_comparable',
                'unit_of_measure' => '% / jaar',
            ],
        ];

        foreach ($calculationResults as $data) {
            DB::table('tool_calculation_results')->updateOrInsert(
                [
                    'short' => $data['short'],
                ],
                [
                    'name' => json_encode($data['name']),
                    'help_text' => json_encode($data['help_text'] ?? []),
                    'unit_of_measure' => $data['unit_of_measure'],
                ],
            );
        }
    }
}
