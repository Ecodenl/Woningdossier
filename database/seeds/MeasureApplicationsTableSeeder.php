<?php

use Illuminate\Database\Seeder;

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
                'measure_type' => 'energy_saving',
                'measure_names' => [
                    'nl' => 'Vloerisolatie',
                ],
                'short' => 'floor-insulation',
                'application' => 'place',
                'costs' => 35, // euro
                'cost_unit' => [
                    'nl' => 'per m2',
                ],
                'minimal_costs' => 550, // euro
                'maintenance_interval' => 100,
                'maintenance_unit' => [
                    'nl' => 'per jaar',
                ],
                'step' => 'floor-insulation',
            ],
            [
                'measure_type' => 'energy_saving',
                'measure_names' => [
                    'nl' => 'Bodemisolatie',
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
            ],
            [
                'measure_type' => 'energy_saving',
                'measure_names' => [
                    'nl' => 'Er is nader onderzoek nodig of de vloer geïsoleerd kan worden',
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
            ],
            [
                'measure_type' => 'energy_saving',
                'measure_names' => [
                    'nl' => 'Spouwmuurisolatie',
                ],
                'short' => 'cavity-wall-insulation',
                'application' => 'place',
                'costs' => 19, // euro
                'cost_unit' => [
                    'nl' => 'per m2',
                ],
                'minimal_costs' => 650, // euro
                'maintenance_interval' => 100,
                'maintenance_unit' => [
                    'nl' => 'per jaar',
                ],
                'step' => 'wall-insulation',
            ],
            [
                'measure_type' => 'energy_saving',
                'measure_names' => [
                    'nl' => 'Binnengevelisolatie',
                ],
                'short' => 'facade-wall-insulation',
                'application' => 'place',
                'costs' => 96, // euro
                'cost_unit' => [
                    'nl' => 'per m2',
                ],
                'minimal_costs' => 450, // euro
                'maintenance_interval' => 100,
                'maintenance_unit' => [
                    'nl' => 'per jaar',
                ],
                'step' => 'wall-insulation',
            ],
            [
                'measure_type' => 'energy_saving',
                'measure_names' => [
                    'nl' => 'Er is nader onderzoek nodig hoe de gevel het beste geïsoleerd kan worden',
                ],
                'short' => 'wall-insulation-research',
                'application' => 'place',
                'costs' => 19, // euro
                'cost_unit' => [
                    'nl' => 'per m2',
                ],
                'minimal_costs' => 650, // euro
                'maintenance_interval' => 100,
                'maintenance_unit' => [
                    'nl' => 'jaar',
                ],
                'step' => 'wall-insulation',
            ],
            [ // stap: isolerende beglazing
                'measure_type' => 'energy_saving',
                'measure_names' => [
                    'nl' => 'Glas in lood vervangen',
                ],
                'short' => 'glass-in-lead',
                'application' => 'replace',
                'costs' => 150, // euro
                'cost_unit' => [
                    'nl' => 'per m2',
                ],
                'minimal_costs' => 0, // euro
                'maintenance_interval' => 40,
                'maintenance_unit' => [
                    'nl' => 'jaar',
                ],
                'step' => 'insulated-glazing',
            ],
            [ // stap: isolerende beglazing
                'measure_type' => 'energy_saving',
                'measure_names' => [
                    'nl' => 'Plaatsen van HR++ glas (alleen het glas)',
                ],
                'short' => 'hrpp-glass-only',
                'application' => 'place',
                'costs' => 144, // euro
                'cost_unit' => [
                    'nl' => 'per m2',
                ],
                'minimal_costs' => 0, // euro
                'maintenance_interval' => 40,
                'maintenance_unit' => [
                    'nl' => 'jaar',
                ],
                'step' => 'insulated-glazing',
            ],
            [ // stap: isolerende beglazing
                'measure_type' => 'energy_saving',
                'measure_names' => [
                    'nl' => 'Plaatsen van HR++ glas (inclusief kozijn)',
                ],
                'short' => 'hrpp-glass-frames',
                'application' => 'place',
                'costs' => 550, // euro
                'cost_unit' => [
                    'nl' => 'per m2',
                ],
                'minimal_costs' => 0, // euro
                'maintenance_interval' => 40,
                'maintenance_unit' => [
                    'nl' => 'jaar',
                ],
                'step' => 'insulated-glazing',
            ],
            [ // stap: isolerende beglazing
                'measure_type' => 'energy_saving',
                'measure_names' => [
                    'nl' => 'Plaatsen van drievoudige HR beglazing (inclusief kozijn)',
                ],
                'short' => 'hr3p-frames',
                'application' => 'place',
                'costs' => 700, // euro
                'cost_unit' => [
                    'nl' => 'per m2',
                ],
                'minimal_costs' => 0, // euro
                'maintenance_interval' => 40,
                'maintenance_unit' => [
                    'nl' => 'jaar',
                ],
                'step' => 'insulated-glazing',
            ],
            [
                'measure_type' => 'energy_saving',
                'measure_names' => [
                    'nl' => 'Kierdichting verbeteren',
                ],
                'short' => 'crack-sealing',
                'application' => 'place',
                'costs' => 400, // euro
                'cost_unit' => [
                    'nl' => 'per stuk',
                ],
                'minimal_costs' => 0, // euro
                'maintenance_interval' => 15,
                'maintenance_unit' => [
                    'nl' => 'jaar',
                ],
                'step' => 'insulated-glazing',
            ],
            [
                'measure_type' => 'energy_saving',
                'measure_names' => [
                    'nl' => 'Isolatie hellend dak van binnen uit',
                ],
                'short' => 'roof-insulation-pitched-inside',
                'application' => 'place',
                'costs' => 96, // euro
                'cost_unit' => [
                    'nl' => 'per m2',
                ],
                'minimal_costs' => 650, // euro
                'maintenance_interval' => 100,
                'maintenance_unit' => [
                    'nl' => 'jaar',
                ],
                'step' => 'roof-insulation',
            ],
            [
                'measure_type' => 'energy_saving',
                'measure_names' => [
                    'nl' => 'Isolatie hellend dak met vervanging van de dakpannen',
                ],
                'short' => 'roof-insulation-pitched-replace-tiles',
                'application' => 'replace',
                'costs' => 65, // euro
                'cost_unit' => [
                    'nl' => 'per m2',
                ],
                'minimal_costs' => 1200, // euro
                'maintenance_interval' => 100,
                'maintenance_unit' => [
                    'nl' => 'jaar',
                ],
                'step' => 'roof-insulation',
            ],
            [
                'measure_type' => 'energy_saving',
                'measure_names' => [
                    'nl' => 'Isolatie plat dak op huidige dakbedekking',
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
            ],
            [
                'measure_type' => 'energy_saving',
                'measure_names' => [
                    'nl' => 'Isolatie plat dak met vervanging van de dakbedekking',
                ],
                'short' => 'roof-insulation-flat-replace-current',
                'application' => 'replace',
                'costs' => 30, // euro
                'cost_unit' => [
                    'nl' => 'per m2',
                ],
                'minimal_costs' => 350, // euro
                'maintenance_interval' => 25,
                'maintenance_unit' => [
                    'nl' => 'jaar',
                ],
                'step' => 'roof-insulation',
            ],
            [
                'measure_type' => 'energy_saving',
                'measure_names' => [
                    'nl' => 'Vervangen cv ketel',
                ],
                'short' => 'high-efficiency-boiler-replace',
                'application' => 'replace',
                'costs' => 2100, // euro
                'cost_unit' => [
                    'nl' => 'per stuk',
                ],
                'minimal_costs' => 0, // euro
                'maintenance_interval' => 15,
                'maintenance_unit' => [
                    'nl' => 'jaar',
                ],
                'step' => 'high-efficiency-boiler',
            ],

            [
                'measure_type' => 'energy_saving',
                'measure_names' => [
                    'nl' => 'Plaatsen / vervangen zonneboiler',
                ],
                'short' => 'heater-place-replace',
                'application' => 'place',
                'costs' => 3000, // euro
                'cost_unit' => [
                    'nl' => 'per installatie',
                ],
                'minimal_costs' => 0, // euro
                'maintenance_interval' => 25,
                'maintenance_unit' => [
                    'nl' => 'jaar',
                ],
                'step' => 'heater',
            ],
            [
                'measure_type' => 'energy_saving',
                'measure_names' => [
                    'nl' => 'Plaatsen / vervangen zonnepanelen',
                ],
                'short' => 'solar-panels-place-replace',
                'application' => 'place',
                'costs' => 450, // euro
                'cost_unit' => [
                    'nl' => 'per paneel',
                ],
                'minimal_costs' => 1500, // euro
                'maintenance_interval' => 25,
                'maintenance_unit' => [
                    'nl' => 'jaar',
                ],
                'step' => 'solar-panels',
            ],

            // add more energiebesparende maatregelen here!

            // Onderhoudsmaatregelen
            [
                'measure_type' => 'maintenance',
                'measure_names' => [
                    'nl' => 'Reparatie voegwerk',
                ],
                'short' => 'repair-joint',
                'application' => 'repair',
                'costs' => 55, // euro
                'cost_unit' => [
                    'nl' => 'per m2',
                ],
                'minimal_costs' => 350,
                'maintenance_interval' => 100,
                'maintenance_unit' => [
                    'nl' => 'jaar',
                ],
                'step' => 'wall-insulation',
            ],
            [
                'measure_type' => 'maintenance',
                'measure_names' => [
                    'nl' => 'Reinigen metselwerk',
                ],
                'short' => 'clean-brickwork',
                'application' => 'repair',
                'costs' => 15, // euro
                'cost_unit' => [
                    'nl' => 'per m2',
                ],
                'minimal_costs' => 150,
                'maintenance_interval' => 100,
                'maintenance_unit' => [
                    'nl' => 'jaar',
                ],
                'step' => 'wall-insulation',
            ],
            [
                'measure_type' => 'maintenance',
                'measure_names' => [
                    'nl' => 'Impregneren gevel',
                ],
                'short' => 'impregnate-wall',
                'application' => 'place',
                'costs' => 10, // euro
                'cost_unit' => [
                    'nl' => 'per m2',
                ],
                'minimal_costs' => 150,
                'maintenance_interval' => 15,
                'maintenance_unit' => [
                    'nl' => 'jaar',
                ],
                'step' => 'wall-insulation',
            ],
            [
                'measure_type' => 'maintenance',
                'measure_names' => [
                    'nl' => 'Gevelschilderwerk op stuk- of metselwerk',
                ],
                'short' => 'paint-wall',
                'application' => 'place',
                'costs' => 35, // euro
                'cost_unit' => [
                    'nl' => 'per m2',
                ],
                'minimal_costs' => 350,
                'maintenance_interval' => 10,
                'maintenance_unit' => [
                    'nl' => 'jaar',
                ],
                'step' => 'wall-insulation',
            ],
            [
                'measure_type' => 'maintenance',
                'measure_names' => [
                    'nl' => 'Schilderwerk houten geveldelen',
                ],
                'short' => 'paint-wood-elements',
                'application' => 'place',
                'costs' => 140, // euro
                'cost_unit' => [
                    'nl' => 'per m2',
                ],
                'minimal_costs' => 400,
                'maintenance_interval' => 7,
                'maintenance_unit' => [
                    'nl' => 'jaar',
                ],
                'step' => 'insulated-glazing',
            ],
            [
                'measure_type' => 'maintenance',
                'measure_names' => [
                    'nl' => 'Vervangen dakpannen',
                ],
                'short' => 'replace-tiles',
                'application' => 'replace',
                'costs' => 135, // euro
                'cost_unit' => [
                    'nl' => 'per m2',
                ],
                'minimal_costs' => 1200,
                'maintenance_interval' => 80,
                'maintenance_unit' => [
                    'nl' => 'jaar',
                ],
                'step' => 'roof-insulation',
            ],
            [
                'measure_type' => 'maintenance',
                'measure_names' => [
                    'nl' => 'Vervangen dakbedekking',
                ],
                'short' => 'replace-roof-insulation',
                'application' => 'replace',
                'costs' => 100, // euro
                'cost_unit' => [
                    'nl' => 'per m2',
                ],
                'minimal_costs' => 700,
                'maintenance_interval' => 25,
                'maintenance_unit' => [
                    'nl' => 'jaar',
                ],
                'step' => 'roof-insulation',
            ],
            [
                'measure_type' => 'maintenance',
                'measure_names' => [
                    'nl' => 'Inspectie en reparatie daken',
                ],
                'short' => 'inspect-repair-roofs',
                'application' => 'replace',
                'costs' => 200, // euro
                'cost_unit' => [
                    'nl' => 'per stuk',
                ],
                'minimal_costs' => 0,
                'maintenance_interval' => 5,
                'maintenance_unit' => [
                    'nl' => 'jaar',
                ],
                'step' => 'roof-insulation',
            ],
            [
                'measure_type' => 'maintenance',
                'measure_names' => [
                    'nl' => 'Zinkwerk hellend dak',
                ],
                'short' => 'replace-zinc-pitched',
                'application' => 'replace',
                'costs' => 100, // euro
                'cost_unit' => [
                    'nl' => 'per m',
                ],
                'minimal_costs' => 250,
                'maintenance_interval' => 25,
                'maintenance_unit' => [
                    'nl' => 'jaar',
                ],
                'step' => 'roof-insulation',
            ],
            [
                'measure_type' => 'maintenance',
                'measure_names' => [
                    'nl' => 'Zinkwerk plat dak',
                ],
                'short' => 'replace-zinc-flat',
                'application' => 'replace',
                'costs' => 25, // euro
                'cost_unit' => [
                    'nl' => 'per m',
                ],
                'minimal_costs' => 250,
                'maintenance_interval' => 25,
                'maintenance_unit' => [
                    'nl' => 'jaar',
                ],
                'step' => 'roof-insulation',
            ],

            // add more onderhoudsmaatregelen here!
        ];

        $translationUUIDs = [];

        foreach ($measureApplications as $measureApplication) {
            foreach ($measureApplication['measure_names'] as $locale => $measureName) {
                if (! array_key_exists('measure_names', $translationUUIDs)) {
                    $translationUUIDs['measure_names'] = [];
                }
                if (! array_key_exists($locale, $translationUUIDs['measure_names'])) {
                    $translationUUIDs['measure_names'][$locale] = [];
                }
                if (! isset($translationUUIDs['measure_names'][$locale][$measureName])) {
                    $mnUuid = \App\Helpers\Str::uuid();
                    \DB::table('translations')->insert([
                        'key'         => $mnUuid,
                        'language'    => $locale,
                        'translation' => $measureName,
                    ]);
                    $translationUUIDs['measure_names'][$locale][$measureName] = $mnUuid;
                } else {
                    $mnUuid = $translationUUIDs['measure_names'][$locale][$measureName];
                }
            }

            foreach ($measureApplication['cost_unit'] as $locale => $costUnitName) {
                if (! array_key_exists('cost_unit', $translationUUIDs)) {
                    $translationUUIDs['cost_unit'] = [];
                }
                if (! array_key_exists($locale, $translationUUIDs['cost_unit'])) {
                    $translationUUIDs['cost_unit'][$locale] = [];
                }
                if (! isset($translationUUIDs['cost_unit'][$locale][$costUnitName])) {
                    $cuUUID = \App\Helpers\Str::uuid();
                    \DB::table('translations')->insert([
                        'key'         => $cuUUID,
                        'language'    => $locale,
                        'translation' => $costUnitName,
                    ]);
                    $translationUUIDs['cost_unit'][$locale][$costUnitName] = $cuUUID;
                } else {
                    $cuUUID = $translationUUIDs['cost_unit'][$locale][$costUnitName];
                }
            }

            foreach ($measureApplication['maintenance_unit'] as $locale => $maintenanceUnitName) {
                if (! array_key_exists('maintenance_unit', $translationUUIDs)) {
                    $translationUUIDs['maintenance_unit'] = [];
                }
                if (! array_key_exists($locale, $translationUUIDs['maintenance_unit'])) {
                    $translationUUIDs['maintenance_unit'][$locale] = [];
                }
                if (! isset($translationUUIDs['maintenance_unit'][$locale][$maintenanceUnitName])) {
                    $muUUID = \App\Helpers\Str::uuid();
                    \DB::table('translations')->insert([
                        'key'         => $muUUID,
                        'language'    => $locale,
                        'translation' => $maintenanceUnitName,
                    ]);
                    $translationUUIDs['maintenance_unit'][$locale][$maintenanceUnitName] = $muUUID;
                } else {
                    $muUUID = $translationUUIDs['maintenance_unit'][$locale][$maintenanceUnitName];
                }
            }

            $step = DB::table('steps')->where('slug', $measureApplication['step'])->first();

            DB::table('measure_applications')->insert([
                'measure_type' => $measureApplication['measure_type'],
                'measure_name' => $mnUuid,
                'short' => $measureApplication['short'],
                'application' => $measureApplication['application'],
                'costs' => $measureApplication['costs'],
                'cost_unit' => $cuUUID,
                'minimal_costs' => $measureApplication['minimal_costs'],
                'maintenance_interval' => $measureApplication['maintenance_interval'],
                'maintenance_unit' => $muUUID,
                'step_id' => $step->id,
            ]);
        }
    }
}
