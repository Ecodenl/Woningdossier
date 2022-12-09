<?php

namespace Database\Seeders;

use App\Models\MeasureApplication;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MeasureApplicationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /*
        $table->enum('measure_type', ['energy_saving', 'maintenance']);
        $table->uuid('measure_name');
        $table->enum('application', ['place', 'replace', 'remove']);
        $table->double('costs', 8, 2);
        $table->uuid('cost_unit');
        $table->double('minimal_costs', 8, 2);
        $table->integer('maintenance_interval');
        $table->uuid('maintenance_unit');
        */

        $measureApplications = [
            // Energiebesparende maatregelen
            [
                'measure_type' => MeasureApplication::ENERGY_SAVING,
                'measure_name' => [
                    'nl' => 'Vloerisolatie',
                ],
                'measure_info' => [
                    'nl' => 'Vloerisolatie helpt om de warmte sneller en gelijkmatiger te verspreiden. Ook tocht en vocht vanuit de kruipruimte is een minder groot probleem als je vloer goed geïsoleerd is. Bovendien gaat er minder warmte verloren, waardoor je minder hoeft te stoken.' . PHP_EOL . 'Je vloer isoleer je via de kruipruimte aan de onderkant van je vloer. Op de bodem van de kruipruimte wordt bij het aanbrengen van de vloerisolatie ook nog een bodemafsluitende folie geplaatst. Hierdoor wordt de vochthuishouding in de kruipruimte beter geregeld.',
                ],
                'short' => 'floor-insulation',
                'application' => 'place',
                'costs' => 45, // euro
                'cost_unit' => [
                    'nl' => 'per m2',
                ],
                'minimal_costs' => 550, // euro
                'maintenance_interval' => 100,
                'maintenance_unit' => [
                    'nl' => 'per jaar',
                ],
                'step' => 'floor-insulation',
                'configurations' => [
                    'comfort' => 5,
                    'icon' => 'icon-floor-insulation-excellent',
                ],
            ],
            [
                'measure_type' => MeasureApplication::ENERGY_SAVING,
                'measure_name' => [
                    'nl' => 'Bodemisolatie',
                ],
                'measure_info' => [
                    'nl' => 'Bodemisolatie helpt om de warmte sneller en gelijkmatiger te verspreiden. Ook tocht en vocht vanuit de kruipruimte is een minder groot probleem als je vloer goed geïsoleerd is. Bovendien gaat er minder warmte verloren, waardoor je minder hoeft te stoken.' . PHP_EOL . 'Bodemisolatie wordt geplaatst door een laag met isolatie chips op de bodem van de kruipruimte aan te brengen. Deze manier van isoleren is vooral geschikt voor lage en/of vochtige kruipruimtes. ',
                ],
                'short' => 'bottom-insulation',
                'application' => 'place',
                'costs' => 25, // euro
                'cost_unit' => [
                    'nl' => 'per m2',
                ],
                'minimal_costs' => 400, // euro
                'maintenance_interval' => 40,
                'maintenance_unit' => [
                    'nl' => 'per jaar',
                ],
                'step' => 'floor-insulation',
                'configurations' => [
                    'comfort' => 3,
                    'icon' => 'icon-floor-insulation-good',
                ],
            ],
            [
                'measure_type' => MeasureApplication::ENERGY_SAVING,
                'measure_name' => [
                    'nl' => 'Vloerisolatie, meer info nodig',
                ],
                'measure_info' => [
                    'nl' => '',
                ],
                'short' => 'floor-insulation-research',
                'application' => 'place',
                'costs' => 25, // euro
                'cost_unit' => [
                    'nl' => 'per m2',
                ],
                'minimal_costs' => 400, // euro
                'maintenance_interval' => 40,
                'maintenance_unit' => [
                    'nl' => 'per jaar',
                ],
                'step' => 'floor-insulation',
                'configurations' => [
                    'comfort' => 2,
                    'icon' => 'icon-floor-insulation-moderate',
                ],
            ],
            [
                'measure_type' => MeasureApplication::ENERGY_SAVING,
                'measure_name' => [
                    'nl' => 'Spouwmuurisolatie',
                ],
                'measure_info' => [
                    'nl' => 'Geïsoleerde gevels helpen om het comfortniveau van je woning omhoog te brengen omdat er minder kou vanuit de muren komt. Bovendien heb je vaak lagere energielasten en vermindert de isolatie het risico op condens en schimmelvorming op de muren.' . PHP_EOL . 'Jouw woning heeft spouwmuren. Een spouwmuur bestaat uit een binnenmuur en een buitenmuur. Daartussen zit een paar centimeter lucht: de spouw. Die spouw kan bij jouw woning worden gevuld met isolatiemateriaal. Dit is een eenvoudige maatregel met een goede verhouding tussen kosten en opbrengsten. ',
                ],
                'short' => 'cavity-wall-insulation',
                'application' => 'place',
                'costs' => 29, // euro
                'cost_unit' => [
                    'nl' => 'per m2',
                ],
                'minimal_costs' => 650, // euro
                'maintenance_interval' => 100,
                'maintenance_unit' => [
                    'nl' => 'per jaar',
                ],
                'step' => 'wall-insulation',
                'configurations' => [
                    'comfort' => 3,
                    'icon' => 'icon-wall-insulation-good',
                ],
            ],
            [
                'measure_type' => MeasureApplication::ENERGY_SAVING,
                'measure_name' => [
                    'nl' => 'Binnengevelisolatie',
                ],
                'measure_info' => [
                    'nl' => 'Geïsoleerde gevels helpen om het comfortniveau van je woning omhoog te brengen omdat er minder kou vanuit de muren komt. Bovendien heb je vaak lagere energielasten en vermindert de isolatie het risico op condens en schimmelvorming op de muren.' . PHP_EOL . 'Jouw woning heeft geen spouwmuur die geïsoleerd kan worden en ook aan de buitenkant kun of mag je niets veranderen. Daarom kun je in jouw geval het beste geïsoleerde voorzetwanden plaatsen. Je offert een paar centimeter woonoppervlakte op, maar je krijgt er een comfortabeler en energiezuiniger huis voor terug.',
                ],
                'short' => 'facade-wall-insulation',
                'application' => 'place',
                'costs' => 114, // euro
                'cost_unit' => [
                    'nl' => 'per m2',
                ],
                'minimal_costs' => 450, // euro
                'maintenance_interval' => 100,
                'maintenance_unit' => [
                    'nl' => 'per jaar',
                ],
                'step' => 'wall-insulation',
                'configurations' => [
                    'comfort' => 5,
                    'icon' => 'icon-wall-insulation-excellent',
                ],
            ],
            [
                'measure_type' => MeasureApplication::ENERGY_SAVING,
                'measure_name' => [
                    'nl' => 'Muurisolatie, meer info nodig',
                ],
                'measure_info' => [
                    'nl' => '',
                ],
                'short' => 'wall-insulation-research',
                'application' => 'place',
                'costs' => 29, // euro
                'cost_unit' => [
                    'nl' => 'per m2',
                ],
                'minimal_costs' => 650, // euro
                'maintenance_interval' => 100,
                'maintenance_unit' => [
                    'nl' => 'jaar',
                ],
                'step' => 'wall-insulation',
                'configurations' => [
                    'comfort' => 3,
                    'icon' => 'icon-wall-insulation-moderate',
                ],
            ],
            [ // stap: isolerende beglazing
                'measure_type' => MeasureApplication::ENERGY_SAVING,
                'measure_name' => [
                    'nl' => 'Glas-in-lood vervangen',
                ],
                'measure_info' => [
                    'nl' => '',
                ],
                'short' => 'glass-in-lead',
                'application' => 'replace',
                'costs' => 186, // euro
                'cost_unit' => [
                    'nl' => 'per m2',
                ],
                'minimal_costs' => 0, // euro
                'maintenance_interval' => 40,
                'maintenance_unit' => [
                    'nl' => 'jaar',
                ],
                'step' => 'insulated-glazing',
                'configurations' => [
                    'comfort' => 3,
                    'icon' => 'icon-glass-single',
                ],
            ],
            [ // stap: isolerende beglazing
                'measure_type' => MeasureApplication::ENERGY_SAVING,
                'measure_name' => [
                    'nl' => 'HR++ glas',
                ],
                'measure_info' => [
                    'nl' => 'Enkelglas vervangen door isolatieglas levert niet alleen een aanzienlijke energiebesparing op. Het zorgt ook voor flink wat meer comfort én een betere geluiddemping. Bovendien ben je met ramen en deuren met isolatieglas net wat beter beschermd tegen inbraak. Tegenwoordig wordt standaard dubbel of drievoudig hoog rendement glas (HR++ of HR+++)  toegepast. Dit type beglazing heeft een hoger rendement door een speciale onzichtbare coating, die aan de binnenzijde in de spouw tussen de ruiten aangebracht wordt. Deze coating weerkaatst de warmte en laat het zonlicht door. De isolatiewaarde van glas wordt aangegeven met de zogeheten u-waarde. Hierbij geldt: hoe hoger de u-waarde, hoe slechter de isolerende werking. De standaard waarde voor HR++ glas is tegenwoordig 1,1 W/m2K.',
                ],
                'short' => 'hrpp-glass-only',
                'application' => 'place',
                'costs' => 210, // euro
                'cost_unit' => [
                    'nl' => 'per m2',
                ],
                'minimal_costs' => 0, // euro
                'maintenance_interval' => 40,
                'maintenance_unit' => [
                    'nl' => 'jaar',
                ],
                'step' => 'insulated-glazing',
                'configurations' => [
                    'comfort' => 4,
                    'icon' => 'icon-glass-hr-p',
                ],
            ],
            [ // stap: isolerende beglazing
                'measure_type' => MeasureApplication::ENERGY_SAVING,
                'measure_name' => [
                    'nl' => 'HR++ glas + kozijn',
                ],
                'measure_info' => [
                    'nl' => '',
                ],
                'short' => 'hrpp-glass-frames',
                'application' => 'place',
                'costs' => 645, // euro
                'cost_unit' => [
                    'nl' => 'per m2',
                ],
                'minimal_costs' => 0, // euro
                'maintenance_interval' => 40,
                'maintenance_unit' => [
                    'nl' => 'jaar',
                ],
                'step' => 'insulated-glazing',
                'configurations' => [
                    'comfort' => 4,
                    'icon' => 'icon-glass-hr-dp',
                ],
            ],
            [ // stap: isolerende beglazing
                'measure_type' => MeasureApplication::ENERGY_SAVING,
                'measure_name' => [
                    'nl' => 'HR+++ glas + kozijn',
                ],
                'measure_info' => [
                    'nl' => '',
                ],
                'short' => 'hr3p-frames',
                'application' => 'place',
                'costs' => 754, // euro
                'cost_unit' => [
                    'nl' => 'per m2',
                ],
                'minimal_costs' => 0, // euro
                'maintenance_interval' => 40,
                'maintenance_unit' => [
                    'nl' => 'jaar',
                ],
                'step' => 'insulated-glazing',
                'configurations' => [
                    'comfort' => 5,
                    'icon' => 'icon-glass-hr-tp',
                ],
            ],
            [
                'measure_type' => MeasureApplication::ENERGY_SAVING,
                'measure_name' => [
                    'nl' => 'Kierdichting verbeteren',
                ],
                'measure_info' => [
                    'nl' => 'Door kieren en naden in je woning gaat veel warmte verloren. Bij dakdoorvoeren of slecht sluitende ramen en deuren kun je die kieren en naden dichten. Dit levert direct meer comfort op en het bespaart energie. Vergeet niet om extra aandacht te besteden aan ventilatie in huis. Nu je huis minder ‘tocht’, zul je moeten zorgen voor voldoende aanvoer van frisse lucht. Om te zorgen voor een gezond binnenklimaat moet zowel de aanvoer van frisse lucht als de afvoer van vervuilde lucht continu goed geregeld zijn.',
                ],
                'short' => 'crack-sealing',
                'application' => 'place',
                'costs' => 776, // euro
                'cost_unit' => [
                    'nl' => 'per stuk',
                ],
                'minimal_costs' => 0, // euro
                'maintenance_interval' => 15,
                'maintenance_unit' => [
                    'nl' => 'jaar',
                ],
                'step' => 'insulated-glazing',
                'configurations' => [
                    'comfort' => 3,
                    'icon' => 'icon-cracks-seams',
                ],
            ],
            [
                'measure_type' => MeasureApplication::ENERGY_SAVING,
                'measure_name' => [
                    'nl' => 'Schuin dak isoleren van binnenuit',
                ],
                'measure_info' => [
                    'nl' => 'Een geïsoleerd dak zorgt er ’s winters voor dat het binnen aangenaam warm blijft. In de zomer draagt  dakisolatie juist bij aan het koel houden van de woning. Bovendien worden geluiden van buiten beter gedempt door isolatie.' . PHP_EOL . 'In jouw woning kan het dak het beste aan de binnenkant geïsoleerd worden. Dit kan door isolatiepanelen op het dakbeschot tussen de balken te plaatsen. Dit kun je vervolgens afwerken op een manier die past bij jouw woonstijl. Door deze manier van isoleren offer je een paar centimeter hoogte op voor een comfortabeler en energiezuiniger huis. ',
                ],
                'short' => 'roof-insulation-pitched-inside',
                'application' => 'place',
                'costs' => 78, // euro
                'cost_unit' => [
                    'nl' => 'per m2',
                ],
                'minimal_costs' => 650, // euro
                'maintenance_interval' => 100,
                'maintenance_unit' => [
                    'nl' => 'jaar',
                ],
                'step' => 'roof-insulation',
                'configurations' => [
                    'comfort' => 5,
                    'icon' => 'icon-pitched-roof',
                ],
            ],
            [
                'measure_type' => MeasureApplication::ENERGY_SAVING,
                'measure_name' => [
                    'nl' => 'Schuin dak isoleren + dakpannen vervangen',
                ],
                'measure_info' => [
                    'nl' => '',
                ],
                'short' => 'roof-insulation-pitched-replace-tiles',
                'application' => 'replace',
                'costs' => 240, // euro
                'cost_unit' => [
                    'nl' => 'per m2',
                ],
                'minimal_costs' => 1200, // euro
                'maintenance_interval' => 100,
                'maintenance_unit' => [
                    'nl' => 'jaar',
                ],
                'step' => 'roof-insulation',
                'configurations' => [
                    'comfort' => 5,
                    'icon' => 'icon-pitched-roof',
                ],
            ],
            [
                'measure_type' => MeasureApplication::ENERGY_SAVING,
                'measure_name' => [
                    'nl' => 'Plat dak isoleren op dakbedekking',
                ],
                'measure_info' => [
                    'nl' => 'Een geïsoleerd dak zorgt er ’s winters voor dat het binnen aangenaam warm blijft. In de zomer draagt  dakisolatie juist bij aan het koel houden van de woning. Bovendien worden geluiden van buiten beter gedempt door isolatie.' . PHP_EOL . 'In jouw woning kan het dak het beste aan de buitenkant geïsoleerd worden. Bij een ‘omgekeerd dak’ worden speciale isolatieplaten op de huidige dakbedekking geplaatst. Vervolgens wordt het met een laag grind afgewerkt. De grindlaag zorgt er naast de energiebesparing voor dat de dakbedekking beter beschermd is tegen uv-straling en weersinvloeden. Hierdoor gaat je dakbedekking langer mee!',
                ],
                'short' => 'roof-insulation-flat-current',
                'application' => 'place',
                'costs' => 65, // euro
                'cost_unit' => [
                    'nl' => 'per m2',
                ],
                'minimal_costs' => 350, // euro
                'maintenance_interval' => 25,
                'maintenance_unit' => [
                    'nl' => 'jaar',
                ],
                'step' => 'roof-insulation',
                'configurations' => [
                    'comfort' => 3,
                    'icon' => 'icon-flat-roof',
                ],
            ],
            [
                'measure_type' => MeasureApplication::ENERGY_SAVING,
                'measure_name' => [
                    'nl' => 'Plat dak isoleren + dakbedekking vervangen',
                ],
                'measure_info' => [
                    'nl' => '',
                ],
                'short' => 'roof-insulation-flat-replace-current',
                'application' => 'replace',
                'costs' => 275, // euro
                'cost_unit' => [
                    'nl' => 'per m2',
                ],
                'minimal_costs' => 350, // euro
                'maintenance_interval' => 25,
                'maintenance_unit' => [
                    'nl' => 'jaar',
                ],
                'step' => 'roof-insulation',
                'configurations' => [
                    'comfort' => 5,
                    'icon' => 'icon-flat-roof',
                ],
            ],
            [
                'measure_type' => MeasureApplication::ENERGY_SAVING,
                'measure_name' => [
                    'nl' => 'CV-ketel vervangen ',
                ],
                'measure_info' => [
                    'nl' => 'Jouw huis wordt verwarmd door een oude hoogrendementsketel (HR ketel). Aan de gaskeurlabels kun je zien tot welke klasse de ketel behoort. Moderne ketels hebben het HR107 label voor verwarming en het HRww label voor het verwarmen van tapwater. Het gaskeurlabel CW (met een cijfer van 1 tot en met 6) geeft het comfort aan voor warm tapwater. Hoe hoger de cijfer, hoe meer warm tapwater de ketel tegelijkertijd kan leveren. Een HR ketel vervangen door een HR107 ketel levert je een beperkte energiebesparing op.',
                ],
                'short' => 'high-efficiency-boiler-replace',
                'application' => 'replace',
                'costs' => 2741, // euro
                'cost_unit' => [
                    'nl' => 'per stuk',
                ],
                'minimal_costs' => 0, // euro
                'maintenance_interval' => 15,
                'maintenance_unit' => [
                    'nl' => 'jaar',
                ],
                'step' => 'high-efficiency-boiler',
                'configurations' => [
                    'comfort' => 3,
                    'icon' => 'icon-central-heater',
                ],
            ],
            [
                'measure_type' => MeasureApplication::ENERGY_SAVING,
                'measure_name' => [
                    'nl' => 'Zonneboiler plaatsen/vervangen',
                ],
                'measure_info' => [
                    'nl' => 'Met een zonneboiler kun je ongeveer de helft van je energieverbruik voor warm tapwater opwekken. Er zijn twee soorten zonneboilers, een zonneboiler voor warm tapwater en de zonneboilercombi voor warm tapwater en cv-ondersteuning.' . PHP_EOL . 'Met een zonnewarmtesysteem wordt het water met een collector op het dak opgewarmd en opgeslagen in een voorraadvat. Levert de collector niet voldoende warmte? Dan wordt het water naverwarmd, bijvoorbeeld door de CV ketel. Alle gastoestellen met het NZ-label (Naverwarming-Zonneboiler) kun je combineren met een zonneboiler.',
                ],
                'short' => 'heater-place-replace',
                'application' => 'place',
                'costs' => 3677, // euro
                'cost_unit' => [
                    'nl' => 'per installatie',
                ],
                'minimal_costs' => 0, // euro
                'maintenance_interval' => 25,
                'maintenance_unit' => [
                    'nl' => 'jaar',
                ],
                'step' => 'heater',
                'configurations' => [
                    'comfort' => 3,
                    'icon' => 'icon-sun-boiler',
                ],
            ],
            [
                'measure_type' => MeasureApplication::ENERGY_SAVING,
                'measure_name' => [
                    'nl' => 'Zonnepanelen plaatsen/vervangen',
                ],
                'measure_info' => [
                    'nl' => 'Gemiddeld bestaat onze energierekening voor één derde uit elektriciteitskosten. En de vraag naar stroom zal in de toekomst alleen maar groter worden. Denk aan meer elektrische apparaten in huis, een elektrische fiets of scooter die opgeladen moet worden of zelfs een elektrische auto. Het helpt om te kiezen voor energiezuinige alternatieven, maar ook zonnenpanelen zijn een goede manier om de energierekening omlaag te krijgen.' . PHP_EOL . 'Tegenwoordig hoef je geen dak op zuid meer te hebben om zonnepanelen rendabel te laten zijn. De huidige zonnepanelen zijn zo efficiënt dat ook daken op het oosten of westen geschikt zijn voor een zonnestroomsysteem.',
                ],
                'short' => 'solar-panels-place-replace',
                'application' => 'place',
                'costs' => 387, // euro
                'cost_unit' => [
                    'nl' => 'per paneel',
                ],
                'minimal_costs' => 1500, // euro
                'maintenance_interval' => 25,
                'maintenance_unit' => [
                    'nl' => 'jaar',
                ],
                'step' => 'solar-panels',
                'configurations' => [
                    'comfort' => 1,
                    'icon' => 'icon-solar-panels',
                ],
            ],
            [
                'measure_type' => MeasureApplication::ENERGY_SAVING,
                'measure_name' => [
                    'nl' => 'Gebalanceerde ventilatie',
                ],
                'measure_info' => [
                    'nl' => '',
                ],
                'short' => 'ventilation-balanced-wtw',
                'application' => 'place',
                'costs' => 0,
                'cost_unit' => [
                    'nl' => 'per stuk',
                ],
                'minimal_costs' => 0,
                'maintenance_interval' => 0,
                'maintenance_unit' => [
                    'nl' => 'jaar',
                ],
                'step' => 'ventilation',
                'configurations' => [
                    'comfort' => 5,
                    'icon' => 'icon-ventilation',
                ],
            ],
            [
                'measure_type' => MeasureApplication::ENERGY_SAVING,
                'measure_name' => [
                    'nl' => 'Decentrale mechanische ventilatie',
                ],
                'measure_info' => [
                    'nl' => 'Mechanische ventilatie zorgt ervoor dat er voortdurend lucht afgevoerd wordt in je huis. Door open ventilatieroosters komt er schone lucht naar binnen. Natuurlijk wil je het hele jaar door een gezond binnenklimaat. Daarom zijn ventilatiesystemen ontwikkelt om 365 dagen per jaar te werken. Maar, oude wisselstroom ventilatoren gebruiken veel meer elektriciteit dan moderne gelijkstroom ventilatoren. Vervang je een oude ventilator door een moderne, kan dat je een besparing tot wel 80% opleveren.' . PHP_EOL . 'Een ventilator vervangen is gelukkig een redelijk eenvoudige maatregel. Oude aansluitingen en leidingen kun je vaak opnieuw gebruiken.',
                ],
                'short' => 'ventilation-decentral-wtw',
                'application' => 'place',
                'costs' => 0,
                'cost_unit' => [
                    'nl' => 'per stuk',
                ],
                'minimal_costs' => 0,
                'maintenance_interval' => 0,
                'maintenance_unit' => [
                    'nl' => 'jaar',
                ],
                'step' => 'ventilation',
                'configurations' => [
                    'comfort' => 5,
                    'icon' => 'icon-ventilation',
                ],
            ],
            [
                'measure_type' => MeasureApplication::ENERGY_SAVING,
                'measure_name' => [
                    'nl' => 'Vraaggestuurde ventilatie',
                ],
                'measure_info' => [
                    'nl' => '',
                ],
                'short' => 'ventilation-demand-driven',
                'application' => 'place',
                'costs' => 0,
                'cost_unit' => [
                    'nl' => 'per stuk',
                ],
                'minimal_costs' => 0,
                'maintenance_interval' => 0,
                'maintenance_unit' => [
                    'nl' => 'jaar',
                ],
                'step' => 'ventilation',
                'configurations' => [
                    'comfort' => 4,
                    'icon' => 'icon-ventilation',
                ],
            ],
            [
                'measure_type' => MeasureApplication::ENERGY_SAVING,
                'measure_name' => [
                    'nl' => 'Hybride warmtepomp met buitenlucht',
                ],
                'measure_info' => [
                    'nl' => '',
                ],
                'short' => 'hybrid-heat-pump-outside-air',
                'application' => 'place',
                'costs' => 4500,
                'cost_unit' => [
                    'nl' => 'per stuk',
                ],
                'minimal_costs' => 0,
                'maintenance_interval' => 18,
                'maintenance_unit' => [
                    'nl' => 'jaar',
                ],
                'step' => 'verwarming',
                'configurations' => [
                    'comfort' => 2,
                    'icon' => 'icon-heat-pump-hybrid',
                ],
            ],
            [
                'measure_type' => MeasureApplication::ENERGY_SAVING,
                'measure_name' => [
                    'nl' => 'Hybride warmtepomp met ventilatielucht',
                ],
                'measure_info' => [
                    'nl' => '',
                ],
                'short' => 'hybrid-heat-pump-ventilation-air',
                'application' => 'place',
                'costs' => 3500,
                'cost_unit' => [
                    'nl' => 'per stuk',
                ],
                'minimal_costs' => 0,
                'maintenance_interval' => 18,
                'maintenance_unit' => [
                    'nl' => 'jaar',
                ],
                'step' => 'verwarming',
                'configurations' => [
                    'comfort' => 2,
                    'icon' => 'icon-heat-pump-hybrid',
                ],
            ],
            [
                'measure_type' => MeasureApplication::ENERGY_SAVING,
                'measure_name' => [
                    'nl' => 'Hybride warmtepomp met pvt panelen',
                ],
                'measure_info' => [
                    'nl' => '',
                ],
                'short' => 'hybrid-heat-pump-pvt-panels',
                'application' => 'place',
                'costs' => 11000,
                'cost_unit' => [
                    'nl' => 'per stuk',
                ],
                'minimal_costs' => 0,
                'maintenance_interval' => 18,
                'maintenance_unit' => [
                    'nl' => 'jaar',
                ],
                'step' => 'verwarming',
                'configurations' => [
                    'comfort' => 2,
                    'icon' => 'icon-heat-pump-hybrid',
                ],
            ],
            [
                'measure_type' => MeasureApplication::ENERGY_SAVING,
                'measure_name' => [
                    'nl' => 'Volledige warmtepomp met buitenlucht',
                ],
                'measure_info' => [
                    'nl' => '',
                ],
                'short' => 'full-heat-pump-outside-air',
                'application' => 'place',
                'costs' => 12000,
                'cost_unit' => [
                    'nl' => 'per stuk',
                ],
                'minimal_costs' => 0,
                'maintenance_interval' => 18,
                'maintenance_unit' => [
                    'nl' => 'jaar',
                ],
                'step' => 'verwarming',
                'configurations' => [
                    'comfort' => 3,
                    'icon' => 'icon-heat-pump',
                ],
            ],
            [
                'measure_type' => MeasureApplication::ENERGY_SAVING,
                'measure_name' => [
                    'nl' => 'Volledige warmtepomp met bodemwarmte',
                ],
                'measure_info' => [
                    'nl' => '',
                ],
                'short' => 'full-heat-pump-ground-heat',
                'application' => 'place',
                'costs' => 29000,
                'cost_unit' => [
                    'nl' => 'per stuk',
                ],
                'minimal_costs' => 0,
                'maintenance_interval' => 18,
                'maintenance_unit' => [
                    'nl' => 'jaar',
                ],
                'step' => 'verwarming',
                'configurations' => [
                    'comfort' => 3,
                    'icon' => 'icon-heat-pump',
                ],
            ],
            [
                'measure_type' => MeasureApplication::ENERGY_SAVING,
                'measure_name' => [
                    'nl' => 'Volledige warmtepomp met pvt panelen',
                ],
                'measure_info' => [
                    'nl' => '',
                ],
                'short' => 'full-heat-pump-pvt-panels',
                'application' => 'place',
                'costs' => 24000,
                'cost_unit' => [
                    'nl' => 'per stuk',
                ],
                'minimal_costs' => 0,
                'maintenance_interval' => 18,
                'maintenance_unit' => [
                    'nl' => 'jaar',
                ],
                'step' => 'verwarming',
                'configurations' => [
                    'comfort' => 3,
                    'icon' => 'icon-heat-pump',
                ],
            ],
            [
                'measure_type' => MeasureApplication::ENERGY_SAVING,
                'measure_name' => [
                    'nl' => 'Warmtepompboiler plaatsen/vervangen',
                ],
                'measure_info' => [
                    'nl' => '',
                ],
                'short' => 'heat-pump-boiler-place-replace',
                'application' => 'place',
                'costs' => 25000,
                'cost_unit' => [
                    'nl' => 'per stuk',
                ],
                'minimal_costs' => 0,
                'maintenance_interval' => 0,
                'maintenance_unit' => [
                    'nl' => 'jaar',
                ],
                'step' => 'verwarming',
                'configurations' => [
                    'comfort' => 3,
                    'icon' => 'icon-placeholder',
                ],
            ],
            [
                'measure_type' => MeasureApplication::ENERGY_SAVING,
                'measure_name' => [
                    'nl' => 'Besparen met verlichting',
                ],
                'measure_info' => [
                    'nl' => '',
                ],
                'short' => 'save-energy-with-light',
                'application' => 'place',
                'costs' => 45, // euro
                'cost_unit' => [
                    'nl' => 'per maatregel',
                ],
                'minimal_costs' => 0,
                'maintenance_interval' => 0,
                'maintenance_unit' => [
                    'nl' => 'jaar',
                ],
                'step' => 'kleine-maatregelen',
                'configurations' => [
                    'comfort' => 0,
                    'icon' => 'icon-tools',
                ],
            ],
            [
                'measure_type' => MeasureApplication::ENERGY_SAVING,
                'measure_name' => [
                    'nl' => 'Energiezuinige apparatuur',
                ],
                'measure_info' => [
                    'nl' => '',
                ],
                'short' => 'energy-efficient-equipment',
                'application' => 'place',
                'costs' => 45, // euro
                'cost_unit' => [
                    'nl' => 'per maatregel',
                ],
                'minimal_costs' => 0,
                'maintenance_interval' => 0,
                'maintenance_unit' => [
                    'nl' => 'jaar',
                ],
                'step' => 'kleine-maatregelen',
                'configurations' => [
                    'comfort' => 0,
                    'icon' => 'icon-tools',
                ],
            ],
            [
                'measure_type' => MeasureApplication::ENERGY_SAVING,
                'measure_name' => [
                    'nl' => 'Energiezuinige installaties',
                ],
                'measure_info' => [
                    'nl' => '',
                ],
                'short' => 'energy-efficient-installations',
                'application' => 'place',
                'costs' => 45, // euro
                'cost_unit' => [
                    'nl' => 'per maatregel',
                ],
                'minimal_costs' => 0,
                'maintenance_interval' => 0,
                'maintenance_unit' => [
                    'nl' => 'jaar',
                ],
                'step' => 'kleine-maatregelen',
                'configurations' => [
                    'comfort' => 0,
                    'icon' => 'icon-tools',
                ],
            ],
            [
                'measure_type' => MeasureApplication::ENERGY_SAVING,
                'measure_name' => [
                    'nl' => 'Besparen door kierdichting',
                ],
                'measure_info' => [
                    'nl' => '',
                ],
                'short' => 'save-energy-with-crack-sealing',
                'application' => 'place',
                'costs' => 45, // euro
                'cost_unit' => [
                    'nl' => 'per maatregel',
                ],
                'minimal_costs' => 0,
                'maintenance_interval' => 0,
                'maintenance_unit' => [
                    'nl' => 'jaar',
                ],
                'step' => 'kleine-maatregelen',
                'configurations' => [
                    'comfort' => 0,
                    'icon' => 'icon-tools',
                ],
            ],
            [
                'measure_type' => MeasureApplication::ENERGY_SAVING,
                'measure_name' => [
                    'nl' => 'Verbeteren van de radiatoren',
                ],
                'measure_info' => [
                    'nl' => '',
                ],
                'short' => 'improve-radiators',
                'application' => 'place',
                'costs' => 45, // euro
                'cost_unit' => [
                    'nl' => 'per maatregel',
                ],
                'minimal_costs' => 0,
                'maintenance_interval' => 0,
                'maintenance_unit' => [
                    'nl' => 'jaar',
                ],
                'step' => 'kleine-maatregelen',
                'configurations' => [
                    'comfort' => 0,
                    'icon' => 'icon-tools',
                ],
            ],
            [
                'measure_type' => MeasureApplication::ENERGY_SAVING,
                'measure_name' => [
                    'nl' => 'Verbeteren van de verwarmingsinstallatie',
                ],
                'measure_info' => [
                    'nl' => '',
                ],
                'short' => 'improve-heating-installations',
                'application' => 'place',
                'costs' => 45, // euro
                'cost_unit' => [
                    'nl' => 'per maatregel',
                ],
                'minimal_costs' => 0,
                'maintenance_interval' => 0,
                'maintenance_unit' => [
                    'nl' => 'jaar',
                ],
                'step' => 'kleine-maatregelen',
                'configurations' => [
                    'comfort' => 0,
                    'icon' => 'icon-tools',
                ],
            ],
            [
                'measure_type' => MeasureApplication::ENERGY_SAVING,
                'measure_name' => [
                    'nl' => 'Besparen op warm tapwater',
                ],
                'measure_info' => [
                    'nl' => '',
                ],
                'short' => 'save-energy-with-warm-tap-water',
                'application' => 'place',
                'costs' => 45, // euro
                'cost_unit' => [
                    'nl' => 'per maatregel',
                ],
                'minimal_costs' => 0,
                'maintenance_interval' => 0,
                'maintenance_unit' => [
                    'nl' => 'jaar',
                ],
                'step' => 'kleine-maatregelen',
                'configurations' => [
                    'comfort' => 0,
                    'icon' => 'icon-tools',
                ],
            ],
            [
                'measure_type' => MeasureApplication::ENERGY_SAVING,
                'measure_name' => [
                    'nl' => 'Algemeen',
                ],
                'measure_info' => [
                    'nl' => '',
                ],
                'short' => 'general',
                'application' => 'place',
                'costs' => 45, // euro
                'cost_unit' => [
                    'nl' => 'per maatregel',
                ],
                'minimal_costs' => 0,
                'maintenance_interval' => 0,
                'maintenance_unit' => [
                    'nl' => 'jaar',
                ],
                'step' => 'kleine-maatregelen',
                'configurations' => [
                    'comfort' => 0,
                    'icon' => 'icon-tools',
                ],
            ],
            // add more energiebesparende maatregelen here!

            // Onderhoudsmaatregelen
            [
                'measure_type' => MeasureApplication::MAINTENANCE,
                'measure_name' => [
                    'nl' => 'Voegwerk repareren',
                ],
                'measure_info' => [
                    'nl' => '',
                ],
                'short' => 'repair-joint',
                'application' => 'repair',
                'costs' => 64, // euro
                'cost_unit' => [
                    'nl' => 'per m2',
                ],
                'minimal_costs' => 350,
                'maintenance_interval' => 100,
                'maintenance_unit' => [
                    'nl' => 'jaar',
                ],
                'step' => 'wall-insulation',
                'configurations' => [
                    'comfort' => 0,
                    'icon' => 'icon-tools',
                ],
            ],
            [
                'measure_type' => MeasureApplication::MAINTENANCE,
                'measure_name' => [
                    'nl' => 'Metselwerk reinigen',
                ],
                'measure_info' => [
                    'nl' => '',
                ],
                'short' => 'clean-brickwork',
                'application' => 'repair',
                'costs' => 18, // euro
                'cost_unit' => [
                    'nl' => 'per m2',
                ],
                'minimal_costs' => 150,
                'maintenance_interval' => 100,
                'maintenance_unit' => [
                    'nl' => 'jaar',
                ],
                'step' => 'wall-insulation',
                'configurations' => [
                    'comfort' => 0,
                    'icon' => 'icon-tools',
                ],
            ],
            [
                'measure_type' => MeasureApplication::MAINTENANCE,
                'measure_name' => [
                    'nl' => 'Gevelimpregnatie',
                ],
                'measure_info' => [
                    'nl' => '',
                ],
                'short' => 'impregnate-wall',
                'application' => 'place',
                'costs' => 12, // euro
                'cost_unit' => [
                    'nl' => 'per m2',
                ],
                'minimal_costs' => 150,
                'maintenance_interval' => 15,
                'maintenance_unit' => [
                    'nl' => 'jaar',
                ],
                'step' => 'wall-insulation',
                'configurations' => [
                    'comfort' => 0,
                    'icon' => 'icon-hydronic-balance-temperature',
                ],
            ],
            [
                'measure_type' => MeasureApplication::MAINTENANCE,
                'measure_name' => [
                    'nl' => 'Gevelschilderwerk (stuc- of metselwerk)',
                ],
                'measure_info' => [
                    'nl' => '',
                ],
                'short' => 'paint-wall',
                'application' => 'place',
                'costs' => 41, // euro
                'cost_unit' => [
                    'nl' => 'per m2',
                ],
                'minimal_costs' => 350,
                'maintenance_interval' => 10,
                'maintenance_unit' => [
                    'nl' => 'jaar',
                ],
                'step' => 'wall-insulation',
                'configurations' => [
                    'comfort' => 0,
                    'icon' => 'icon-paint-job',
                ],
            ],
            [
                'measure_type' => MeasureApplication::MAINTENANCE,
                'measure_name' => [
                    'nl' => 'Gevelschilderwerk (hout)',
                ],
                'measure_info' => [
                    'nl' => '',
                ],
                'short' => 'paint-wood-elements',
                'application' => 'place',
                'costs' => 164, // euro
                'cost_unit' => [
                    'nl' => 'per m2',
                ],
                'minimal_costs' => 400,
                'maintenance_interval' => 7,
                'maintenance_unit' => [
                    'nl' => 'jaar',
                ],
                'step' => 'insulated-glazing',
                'configurations' => [
                    'comfort' => 0,
                    'icon' => 'icon-paint-job',
                ],
            ],
            [
                'measure_type' => MeasureApplication::MAINTENANCE,
                'measure_name' => [
                    'nl' => 'Dakpannen vervangen',
                ],
                'measure_info' => [
                    'nl' => '',
                ],
                'short' => 'replace-tiles',
                'application' => 'replace',
                'costs' => 158, // euro
                'cost_unit' => [
                    'nl' => 'per m2',
                ],
                'minimal_costs' => 1200,
                'maintenance_interval' => 80,
                'maintenance_unit' => [
                    'nl' => 'jaar',
                ],
                'step' => 'roof-insulation',
                'configurations' => [
                    'comfort' => 0,
                    'icon' => 'icon-tools',
                ],
            ],
            [
                'measure_type' => MeasureApplication::MAINTENANCE,
                'measure_name' => [
                    'nl' => 'Dakbedekking vervangen',
                ],
                'measure_info' => [
                    'nl' => '',
                ],
                'short' => 'replace-roof-insulation',
                'application' => 'replace',
                'costs' => 117, // euro
                'cost_unit' => [
                    'nl' => 'per m2',
                ],
                'minimal_costs' => 700,
                'maintenance_interval' => 25,
                'maintenance_unit' => [
                    'nl' => 'jaar',
                ],
                'step' => 'roof-insulation',
                'configurations' => [
                    'comfort' => 0,
                    'icon' => 'icon-roof-insulation-excellent',
                ],
            ],
            [
                'measure_type' => MeasureApplication::MAINTENANCE,
                'measure_name' => [
                    'nl' => 'Dakreparatie',
                ],
                'measure_info' => [
                    'nl' => '',
                ],
                'short' => 'inspect-repair-roofs',
                'application' => 'replace',
                'costs' => 234, // euro
                'cost_unit' => [
                    'nl' => 'per stuk',
                ],
                'minimal_costs' => 0,
                'maintenance_interval' => 5,
                'maintenance_unit' => [
                    'nl' => 'jaar',
                ],
                'step' => 'roof-insulation',
                'configurations' => [
                    'comfort' => 0,
                    'icon' => 'icon-tools',
                ],
            ],
            [
                'measure_type' => MeasureApplication::MAINTENANCE,
                'measure_name' => [
                    'nl' => 'Zinkwerk schuin dak',
                ],
                'measure_info' => [
                    'nl' => '',
                ],
                'short' => 'replace-zinc-pitched',
                'application' => 'replace',
                'costs' => 120, // euro
                'cost_unit' => [
                    'nl' => 'per m',
                ],
                'minimal_costs' => 250,
                'maintenance_interval' => 25,
                'maintenance_unit' => [
                    'nl' => 'jaar',
                ],
                'step' => 'roof-insulation',
                'configurations' => [
                    'comfort' => 0,
                    'icon' => 'icon-pitched-roof',
                ],
            ],
            [
                'measure_type' => MeasureApplication::MAINTENANCE,
                'measure_name' => [
                    'nl' => 'Zinkwerk plat dak',
                ],
                'measure_info' => [
                    'nl' => '',
                ],
                'short' => 'replace-zinc-flat',
                'application' => 'replace',
                'costs' => 30, // euro
                'cost_unit' => [
                    'nl' => 'per m',
                ],
                'minimal_costs' => 250,
                'maintenance_interval' => 25,
                'maintenance_unit' => [
                    'nl' => 'jaar',
                ],
                'step' => 'roof-insulation',
                'configurations' => [
                    'comfort' => 0,
                    'icon' => 'icon-flat-roof',
                ],
            ],

            // add more onderhoudsmaatregelen here!
        ];

        foreach ($measureApplications as $measureApplication) {
            // Some steps exist more than once but in that case it doesn't matter as we won't link to them
            // based on step.
            $step = DB::table('steps')->where('slug', $measureApplication['step'])->first();

            $existingMeasureApplication = DB::table('measure_applications')
                ->where('short', $measureApplication['short'])->first();

            $name = $measureApplication['measure_name'];
            $info = $measureApplication['measure_info'];
            $configurations = $measureApplication['configurations'];

            // Ensure we don't override
            if ($existingMeasureApplication instanceof \stdClass) {
                $name = json_decode($existingMeasureApplication->measure_name, true);
                $info = json_decode($existingMeasureApplication->measure_info, true);

                $savedConfig = json_decode($existingMeasureApplication->measure_info, true);
                if (! empty($savedConfig['icon'])) {
                    $configurations['icon'] = $savedConfig['icon'];
                }
            }

            DB::table('measure_applications')->updateOrInsert(
                [
                    'short' => $measureApplication['short'],
                ],
                [
                    'measure_type' => $measureApplication['measure_type'],
                    'measure_name' => json_encode($name),
                    'measure_info' => json_encode($info),
                    'application' => $measureApplication['application'],
                    'costs' => $measureApplication['costs'],
                    'cost_unit' => json_encode($measureApplication['cost_unit']),
                    'minimal_costs' => $measureApplication['minimal_costs'],
                    'maintenance_interval' => $measureApplication['maintenance_interval'],
                    'maintenance_unit' => json_encode($measureApplication['maintenance_unit']),
                    'step_id' => $step->id,
                    'configurations' => json_encode($configurations),
                ]
            );
        }
    }
}
