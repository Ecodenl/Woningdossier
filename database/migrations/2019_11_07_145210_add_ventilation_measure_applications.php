<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddVentilationMeasureApplications extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $measureApplications = [
            [
                'measure_type' =>'energy_saving',
                'measure_names' => [
                    'nl' => 'Gebalanceerde ventilatie met warmte terugwinning',
                ],
                'short' => 'ventilation-balanced-wtw',
                'application' => 'place',
                'costs' => 0,
                'cost_unit' => [
                    'nl' => 'per stuk'
                ],
                'minimal_costs' => 0,
                'maintenance_interval' => 0,
                'maintenance_unit' => [
                    'nl' => 'jaar',
                ],
                'step' => 'ventilation',
            ],
            [
                'measure_type' =>'energy_saving',
                'measure_names' => [
                    'nl' => 'Decentrale mechanische ventilatie met warmte terugwinning',
                ],
                'short' => 'ventilation-decentral-wtw',
                'application' => 'place',
                'costs' => 0,
                'cost_unit' => [
                    'nl' => 'per stuk'
                ],
                'minimal_costs' => 0,
                'maintenance_interval' => 0,
                'maintenance_unit' => [
                    'nl' => 'jaar',
                ],
                'step' => 'ventilation',
            ],
            [
                'measure_type' =>'energy_saving',
                'measure_names' => [
                    'nl' => 'Vraag gestuurde ventilatie',
                ],
                'short' => 'ventilation-demand-driven',
                'application' => 'place',
                'costs' => 0,
                'cost_unit' => [
                    'nl' => 'per stuk'
                ],
                'minimal_costs' => 0,
                'maintenance_interval' => 0,
                'maintenance_unit' => [
                    'nl' => 'jaar',
                ],
                'step' => 'ventilation',
            ],
        ];

        $translationUUIDs = [];

        foreach ($measureApplications as $measureApplication) {
            foreach ($measureApplication['measure_names'] as $locale => $measureName) {
                if ( ! array_key_exists('measure_names', $translationUUIDs)) {
                    $translationUUIDs['measure_names'] = [];
                }
                if ( ! array_key_exists($locale,
                    $translationUUIDs['measure_names'])) {
                    $translationUUIDs['measure_names'][$locale] = [];
                }
                if ( ! isset($translationUUIDs['measure_names'][$locale][$measureName])) {
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
                if ( ! array_key_exists('cost_unit', $translationUUIDs)) {
                    $translationUUIDs['cost_unit'] = [];
                }
                if ( ! array_key_exists($locale,
                    $translationUUIDs['cost_unit'])) {
                    $translationUUIDs['cost_unit'][$locale] = [];
                }
                if ( ! isset($translationUUIDs['cost_unit'][$locale][$costUnitName])) {
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
                if ( ! array_key_exists('maintenance_unit',
                    $translationUUIDs)) {
                    $translationUUIDs['maintenance_unit'] = [];
                }
                if ( ! array_key_exists($locale,
                    $translationUUIDs['maintenance_unit'])) {
                    $translationUUIDs['maintenance_unit'][$locale] = [];
                }
                if ( ! isset($translationUUIDs['maintenance_unit'][$locale][$maintenanceUnitName])) {
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

            $step = DB::table('steps')->where('slug',
                $measureApplication['step'])->first();

            DB::table('measure_applications')->updateOrInsert(
                [
                    'short'   => $measureApplication['short'],
                    'step_id' => $step->id,
                ],
                [
                    'measure_type'         => $measureApplication['measure_type'],
                    'measure_name'         => $mnUuid,
                    'application'          => $measureApplication['application'],
                    'costs'                => $measureApplication['costs'],
                    'cost_unit'            => $cuUUID,
                    'minimal_costs'        => $measureApplication['minimal_costs'],
                    'maintenance_interval' => $measureApplication['maintenance_interval'],
                    'maintenance_unit'     => $muUUID,
                ]);
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
