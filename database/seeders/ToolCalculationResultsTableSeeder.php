<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use App\Helpers\DataTypes\Caster;
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
            // HR Boiler
            [
                'name' => [
                    'nl' => 'Huidig gasverbruik',
                ],
                'short' => 'hr-boiler.amount_gas',
                'unit_of_measure' => 'm<sup>3</sup> / jaar',
                'data_type' => Caster::INT_5,
            ],
            [
                'name' => [
                    'nl' => 'Gasbesparing',
                ],
                'short' => 'hr-boiler.savings_gas',
                'unit_of_measure' => 'm<sup>3</sup> / jaar',
                'data_type' => Caster::INT_5,
            ],
            [
                'name' => [
                    'nl' => 'CO2 Besparing',
                ],
                'short' => 'hr-boiler.savings_co2',
                'unit_of_measure' => 'kg / jaar',
                'data_type' => Caster::INT_5,
            ],
            [
                'name' => [
                    'nl' => 'Besparing in €',
                ],
                'short' => 'hr-boiler.savings_money',
                'unit_of_measure' => '<i class="icon-sm icon-moneybag mr-1"></i> / jaar',
                'data_type' => Caster::INT_5,
            ],
            [
                'name' => [
                    'nl' => 'Indicatie vervangingsmoment CV ketel',
                ],
                'short' => 'hr-boiler.replace_year',
                'unit_of_measure' => '<i class="icon-sm icon-timer"></i>',
                'data_type' => Caster::INT,
            ],
            [
                'name' => [
                    'nl' => 'Indicatieve kosten',
                ],
                'short' => 'hr-boiler.cost_indication',
                'unit_of_measure' => '<i class="icon-sm icon-moneybag"></i>',
                'data_type' => Caster::INT_5,
            ],
            [
                'name' => [
                    'nl' => 'Vergelijkbare rente',
                ],
                'short' => 'hr-boiler.interest_comparable',
                'unit_of_measure' => '% / jaar',
                'data_type' => Caster::FLOAT,
            ],
            // Heat pump
            [
                'name' => [
                    'nl' => 'Benodigd indicatief vermogen van de warmtepomp',
                ],
                'short' => 'heat-pump.advised_system.required_power',
                'unit_of_measure' => 'kW',
                'data_type' => Caster::INT,
            ],
            [
                'name' => [
                    'nl' => 'Aandeel van de warmtepomp aan de verwarming',
                ],
                'short' => 'heat-pump.advised_system.share_heating',
                'unit_of_measure' => '%',
                'data_type' => Caster::INT,
            ],
            [
                'name' => [
                    'nl' => 'Aandeel van de warmtepomp aan warm tapwater',
                ],
                'short' => 'heat-pump.advised_system.share_tap_water',
                'unit_of_measure' => '%',
                'data_type' => Caster::INT,
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
                'data_type' => Caster::FLOAT,
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
                'data_type' => Caster::FLOAT,
            ],
            [
                'name' => [
                    'nl' => 'Huidig gasverbruik',
                ],
                'short' => 'heat-pump.amount_gas',
                'unit_of_measure' => 'm<sup>3</sup> / jaar',
                'data_type' => Caster::INT_5,
            ],
            [
                'name' => [
                    'nl' => 'Huidig elektragebruik',
                ],
                'short' => 'heat-pump.amount_electricity',
                'unit_of_measure' => 'kWh / jaar',
                'data_type' => Caster::INT_5,
            ],
            [
                'name' => [
                    'nl' => 'Gasbesparing',
                ],
                'short' => 'heat-pump.savings_gas',
                'unit_of_measure' => 'm<sup>3</sup> / jaar',
                'data_type' => Caster::INT_5,
            ],
            [
                'name' => [
                    'nl' => 'CO2 Besparing',
                ],
                'short' => 'heat-pump.savings_co2',
                'unit_of_measure' => 'kg / jaar',
                'data_type' => Caster::INT_5,
            ],
            [
                'name' => [
                    'nl' => 'Besparing in €',
                ],
                'short' => 'heat-pump.savings_money',
                'unit_of_measure' => '<i class="icon-sm icon-moneybag mr-1"></i> / jaar',
                'data_type' => Caster::INT_5,
            ],
            [
                'name' => [
                    'nl' => 'Meerverbruik elektra',
                ],
                'short' => 'heat-pump.extra_consumption_electricity',
                'unit_of_measure' => 'kWh / jaar',
                'data_type' => Caster::INT_5,
            ],
            [
                'name' => [
                    'nl' => 'Indicatieve kosten',
                ],
                'short' => 'heat-pump.cost_indication',
                'unit_of_measure' => '<i class="icon-sm icon-moneybag"></i>',
                'data_type' => Caster::INT_5,
            ],
            [
                'name' => [
                    'nl' => 'Vergelijkbare rente',
                ],
                'short' => 'heat-pump.interest_comparable',
                'unit_of_measure' => '% / jaar',
                'data_type' => Caster::FLOAT,
            ],
            // Sun boiler
            [
                'name' => [
                    'nl' => 'Gebruik warm tapwater',
                ],
                'help_text' => [
                    'nl' => 'Het gebruik voor warm tapwater is afhankelijk van het aantal gebruikers en het comfortniveau.',
                ],
                'short' => 'sun-boiler.consumption.water',
                'unit_of_measure' => 'liter / jaar',
                'data_type' => Caster::INT_5,
            ],
            [
                'name' => [
                    'nl' => 'Bijbehorend gasverbruik',
                ],
                'help_text' => [
                    'nl' => 'Hier wordt een inschatting gegeven van het jaarlijkse gasverbruik voor warm tapwater.',
                ],
                'short' => 'sun-boiler.consumption.gas',
                'unit_of_measure' => 'm<sup>3</sup> / jaar',
                'data_type' => Caster::INT_5,
            ],
            [
                'name' => [
                    'nl' => 'Grootte zonneboiler',
                ],
                'help_text' => [
                    'nl' => 'Op basis van de verbruikscijfers wordt hier een inschatting gegeven hoe groot het buffervat zou moeten zijn.',
                ],
                'short' => 'sun-boiler.specs.size_boiler',
                'unit_of_measure' => 'liter',
                'data_type' => Caster::INT_5,
            ],
            [
                'name' => [
                    'nl' => 'Grootte collector',
                ],
                'help_text' => [
                    'nl' => 'Op basis van de verbruikscijfers wordt hier een inschatting gegeven hoe groot de collector zou moeten zijn.',
                ],
                'short' => 'sun-boiler.specs.size_collector',
                'unit_of_measure' => 'm<sup>2</sup>',
                'data_type' => Caster::FLOAT,
            ],
            [
                'name' => [
                    'nl' => 'Warmteproductie per jaar',
                ],
                'help_text' => [
                    'nl' => ' Hier kunt u zien hoeveel warmte de zonneboiler per jaar op kan wekken.',
                ],
                'short' => 'sun-boiler.production_heat',
                'unit_of_measure' => 'kWh / jaar',
                'data_type' => Caster::INT_5,
            ],
            [
                'name' => [
                    'nl' => 'Aandeel van de zonneboiler aan het totaalverbruik voor warm water',
                ],
                'help_text' => [
                    'nl' => ' Hier kunt u zien hoeveel % van uw huidig energie voor warm tapwater u met dit zonneboiler systeem kunt opwekken.',
                ],
                'short' => 'sun-boiler.percentage_consumption',
                'unit_of_measure' => '%',
                'data_type' => Caster::INT,
            ],
            [
                'name' => [
                    'nl' => 'Huidig gasverbruik',
                ],
                'short' => 'sun-boiler.amount_gas',
                'unit_of_measure' => 'm<sup>3</sup> / jaar',
                'data_type' => Caster::INT_5,
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
                'data_type' => Caster::INT_5,
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
                'data_type' => Caster::INT_5,
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
                'data_type' => Caster::INT_5,
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
                'data_type' => Caster::INT_5,
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
                'data_type' => Caster::FLOAT,
            ],
            // Ventilation
            [
                'name' => [
                    'nl' => 'Gasbesparing',
                ],
                'short' => 'ventilation.savings_gas',
                'unit_of_measure' => 'm<sup>3</sup> / jaar',
                'data_type' => Caster::INT_5,
            ],
            [
                'name' => [
                    'nl' => 'CO2 Besparing',
                ],
                'short' => 'ventilation.savings_co2',
                'unit_of_measure' => 'kg / jaar',
                'data_type' => Caster::INT_5,
            ],
            [
                'name' => [
                    'nl' => 'Besparing in €',
                ],
                'short' => 'ventilation.savings_money',
                'unit_of_measure' => '<i class="icon-sm icon-moneybag mr-1"></i> / jaar',
                'data_type' => Caster::INT_5,
            ],
            [
                'name' => [
                    'nl' => 'Indicatieve kosten',
                ],
                'short' => 'ventilation.cost_indication',
                'unit_of_measure' => '<i class="icon-sm icon-moneybag"></i>',
                'data_type' => Caster::INT_5,
            ],
            [
                'name' => [
                    'nl' => 'Vergelijkbare rente',
                ],
                'short' => 'ventilation.interest_comparable',
                'unit_of_measure' => '% / jaar',
                'data_type' => Caster::FLOAT,
            ],
            // Wall insulation
            [
                'name' => [
                    'nl' => 'Gasbesparing',
                ],
                'short' => 'wall-insulation.savings_gas',
                'unit_of_measure' => 'm<sup>3</sup> / jaar',
                'data_type' => Caster::INT_5,
            ],
            [
                'name' => [
                    'nl' => 'CO2 Besparing',
                ],
                'short' => 'wall-insulation.savings_co2',
                'unit_of_measure' => 'kg / jaar',
                'data_type' => Caster::INT_5,
            ],
            [
                'name' => [
                    'nl' => 'Besparing in €',
                ],
                'short' => 'wall-insulation.savings_money',
                'unit_of_measure' => '<i class="icon-sm icon-moneybag mr-1"></i> / jaar',
                'data_type' => Caster::INT_5,
            ],
            [
                'name' => [
                    'nl' => 'Indicatieve kosten',
                ],
                'short' => 'wall-insulation.cost_indication',
                'unit_of_measure' => '<i class="icon-sm icon-moneybag"></i>',
                'data_type' => Caster::INT_5,
            ],
            [
                'name' => [
                    'nl' => 'Vergelijkbare rente',
                ],
                'short' => 'wall-insulation.interest_comparable',
                'unit_of_measure' => '% / jaar',
                'data_type' => Caster::FLOAT,
            ],
            [
                'name' => [
                    'nl' => 'Reparatie voegwerk',
                ],
                'short' => 'wall-insulation.repair_joint.costs',
                'unit_of_measure' => '<i class="icon-sm icon-moneybag"></i>',
                'data_type' => Caster::INT_5,
            ],
            [
                'name' => [
                    'nl' => 'Jaar voegwerk',
                ],
                'short' => 'wall-insulation.repair_joint.year',
                'unit_of_measure' => 'jaar',
                'data_type' => Caster::INT,
            ],
            [
                'name' => [
                    'nl' => 'Reinigen metselwerk',
                ],
                'short' => 'wall-insulation.clean_brickwork.costs',
                'unit_of_measure' => '<i class="icon-sm icon-moneybag"></i>',
                'data_type' => Caster::INT_5,
            ],
            [
                'name' => [
                    'nl' => 'Jaar gevelreiniging',
                ],
                'short' => 'wall-insulation.clean_brickwork.year',
                'unit_of_measure' => 'jaar',
                'data_type' => Caster::INT,
            ],
            [
                'name' => [
                    'nl' => 'Impregneren gevel',
                ],
                'short' => 'wall-insulation.impregnate_wall.costs',
                'unit_of_measure' => '<i class="icon-sm icon-moneybag"></i>',
                'data_type' => Caster::INT_5,
            ],
            [
                'name' => [
                    'nl' => 'Jaar gevel impregneren',
                ],
                'short' => 'wall-insulation.impregnate_wall.year',
                'unit_of_measure' => 'jaar',
                'data_type' => Caster::INT,
            ],
            [
                'name' => [
                    'nl' => 'Gevelschilderwerk op stuk of metselwerk',
                ],
                'short' => 'wall-insulation.paint_wall.costs',
                'unit_of_measure' => '<i class="icon-sm icon-moneybag"></i>',
                'data_type' => Caster::INT_5,
            ],
            [
                'name' => [
                    'nl' => 'Jaar gevelschilderwerk',
                ],
                'short' => 'wall-insulation.paint_wall.year',
                'unit_of_measure' => 'jaar',
                'data_type' => Caster::INT,
            ],
            // Ventilation
            [
                'name' => [
                    'nl' => 'Gasbesparing',
                ],
                'short' => 'insulated-glazing.savings_gas',
                'unit_of_measure' => 'm<sup>3</sup> / jaar',
                'data_type' => Caster::INT_5,
            ],
            [
                'name' => [
                    'nl' => 'CO2 Besparing',
                ],
                'short' => 'insulated-glazing.savings_co2',
                'unit_of_measure' => 'kg / jaar',
                'data_type' => Caster::INT_5,
            ],
            [
                'name' => [
                    'nl' => 'Besparing in €',
                ],
                'short' => 'insulated-glazing.savings_money',
                'unit_of_measure' => '<i class="icon-sm icon-moneybag mr-1"></i> / jaar',
                'data_type' => Caster::INT_5,
            ],
            [
                'name' => [
                    'nl' => 'Indicatieve kosten',
                ],
                'short' => 'insulated-glazing.cost_indication',
                'unit_of_measure' => '<i class="icon-sm icon-moneybag"></i>',
                'data_type' => Caster::INT_5,
            ],
            [
                'name' => [
                    'nl' => 'Vergelijkbare rente',
                ],
                'short' => 'insulated-glazing.interest_comparable',
                'unit_of_measure' => '% / jaar',
                'data_type' => Caster::FLOAT,
            ],
            [
                'name' => [
                    'nl' => 'Indicatieve kosten schilderwerk',
                ],
                'short' => 'insulated-glazing.paintwork.costs',
                'unit_of_measure' => '<i class="icon-sm icon-moneybag"></i>',
                'data_type' => Caster::INT_5,
            ],
            [
                'name' => [
                    'nl' => ' Volgende schilderbeurt aanbevolen',
                ],
                'short' => 'insulated-glazing.paintwork.year',
                'unit_of_measure' => 'jaar',
                'data_type' => Caster::INT,
            ],
            // Floor insulation
            [
                'name' => [
                    'nl' => 'Gasbesparing',
                ],
                'short' => 'floor-insulation.savings_gas',
                'unit_of_measure' => 'm<sup>3</sup> / jaar',
                'data_type' => Caster::INT_5,
            ],
            [
                'name' => [
                    'nl' => 'CO2 Besparing',
                ],
                'short' => 'floor-insulation.savings_co2',
                'unit_of_measure' => 'kg / jaar',
                'data_type' => Caster::INT_5,
            ],
            [
                'name' => [
                    'nl' => 'Besparing in €',
                ],
                'short' => 'floor-insulation.savings_money',
                'unit_of_measure' => '<i class="icon-sm icon-moneybag mr-1"></i> / jaar',
                'data_type' => Caster::INT_5,
            ],
            [
                'name' => [
                    'nl' => 'Indicatieve kosten',
                ],
                'short' => 'floor-insulation.cost_indication',
                'unit_of_measure' => '<i class="icon-sm icon-moneybag"></i>',
                'data_type' => Caster::INT_5,
            ],
            [
                'name' => [
                    'nl' => 'Vergelijkbare rente',
                ],
                'short' => 'floor-insulation.interest_comparable',
                'unit_of_measure' => '% / jaar',
                'data_type' => Caster::FLOAT,
            ],
            // Roof insulation
            [
                'name' => [
                    'nl' => 'Gasbesparing',
                ],
                'short' => 'roof-insulation.flat.savings_gas',
                'unit_of_measure' => 'm<sup>3</sup> / jaar',
                'data_type' => Caster::INT_5,
            ],
            [
                'name' => [
                    'nl' => 'CO2 Besparing',
                ],
                'short' => 'roof-insulation.flat.savings_co2',
                'unit_of_measure' => 'kg / jaar',
                'data_type' => Caster::INT_5,
            ],
            [
                'name' => [
                    'nl' => 'Besparing in €',
                ],
                'short' => 'roof-insulation.flat.savings_money',
                'unit_of_measure' => '<i class="icon-sm icon-moneybag mr-1"></i> / jaar',
                'data_type' => Caster::INT_5,
            ],
            [
                'name' => [
                    'nl' => 'Indicatieve kosten',
                ],
                'short' => 'roof-insulation.flat.cost_indication',
                'unit_of_measure' => '<i class="icon-sm icon-moneybag"></i>',
                'data_type' => Caster::INT_5,
            ],
            [
                'name' => [
                    'nl' => 'Vergelijkbare rente',
                ],
                'short' => 'roof-insulation.flat.interest_comparable',
                'unit_of_measure' => '% / jaar',
                'data_type' => Caster::FLOAT,
            ],
            [
                'name' => [
                    'nl' => 'Indicatieve kosten vervanging dakbedekking',
                ],
                'short' => 'roof-insulation.flat.replace.costs',
                'unit_of_measure' => '<i class="icon-sm icon-moneybag"></i>',
                'data_type' => Caster::INT_5,
            ],
            [
                'name' => [
                    'nl' => 'Indicatief vervangingsmoment dakbedekking',
                ],
                'short' => 'roof-insulation.flat.replace.year',
                'unit_of_measure' => 'jaar',
                'data_type' => Caster::INT,
            ],
            [
                'name' => [
                    'nl' => 'Gasbesparing',
                ],
                'short' => 'roof-insulation.pitched.savings_gas',
                'unit_of_measure' => 'm<sup>3</sup> / jaar',
                'data_type' => Caster::INT_5,
            ],
            [
                'name' => [
                    'nl' => 'CO2 Besparing',
                ],
                'short' => 'roof-insulation.pitched.savings_co2',
                'unit_of_measure' => 'kg / jaar',
                'data_type' => Caster::INT_5,
            ],
            [
                'name' => [
                    'nl' => 'Besparing in €',
                ],
                'short' => 'roof-insulation.pitched.savings_money',
                'unit_of_measure' => '<i class="icon-sm icon-moneybag mr-1"></i> / jaar',
                'data_type' => Caster::INT_5,
            ],
            [
                'name' => [
                    'nl' => 'Indicatieve kosten',
                ],
                'short' => 'roof-insulation.pitched.cost_indication',
                'unit_of_measure' => '<i class="icon-sm icon-moneybag"></i>',
                'data_type' => Caster::INT_5,
            ],
            [
                'name' => [
                    'nl' => 'Vergelijkbare rente',
                ],
                'short' => 'roof-insulation.pitched.interest_comparable',
                'unit_of_measure' => '% / jaar',
                'data_type' => Caster::FLOAT,
            ],
            [
                'name' => [
                    'nl' => 'Indicatieve kosten vervanging dakbedekking',
                ],
                'short' => 'roof-insulation.pitched.replace.costs',
                'unit_of_measure' => '<i class="icon-sm icon-moneybag"></i>',
                'data_type' => Caster::INT_5,
            ],
            [
                'name' => [
                    'nl' => 'Indicatief vervangingsmoment dakbedekking',
                ],
                'short' => 'roof-insulation.pitched.replace.year',
                'unit_of_measure' => 'jaar',
                'data_type' => Caster::INT,
            ],
            // Solar panels
            [
                'name' => [
                    'nl' => ' Opbrengst elektra',
                ],
                'short' => 'solar-panels.yield_electricity',
                'unit_of_measure' => 'kWh / jaar',
                'data_type' => Caster::INT_5,
            ],
            [
                'name' => [
                    'nl' => 'Opwekking t.o.v. eigen verbruik ',
                ],
                'short' => 'solar-panels.raise_own_consumption',
                'unit_of_measure' => '%',
                'data_type' => Caster::INT_5,
            ],
            [
                'name' => [
                    'nl' => 'CO2 Besparing',
                ],
                'short' => 'solar-panels.savings_co2',
                'unit_of_measure' => 'kg / jaar',
                'data_type' => Caster::INT_5,
            ],
            [
                'name' => [
                    'nl' => 'Besparing in €',
                ],
                'short' => 'solar-panels.savings_money',
                'unit_of_measure' => '<i class="icon-sm icon-moneybag mr-1"></i> / jaar',
                'data_type' => Caster::INT_5,
            ],
            [
                'name' => [
                    'nl' => 'Indicatieve kosten',
                ],
                'short' => 'solar-panels.cost_indication',
                'unit_of_measure' => '<i class="icon-sm icon-moneybag"></i>',
                'data_type' => Caster::INT_5,
            ],
            [
                'name' => [
                    'nl' => 'Vergelijkbare rente',
                ],
                'short' => 'solar-panels.interest_comparable',
                'unit_of_measure' => '% / jaar',
                'data_type' => Caster::FLOAT,
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
                    'data_type' => $data['data_type'],
                ],
            );
        }
    }
}
