<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class AssessmentTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $types = [
            [
                'type' => 'EPB_ASSESS_CALC_DESIGN',
                'name' => [
                    'en' => 'Calculated design',
                ],
            ],
            [
                'type' => 'EPB_ASSESS_CALC_ASBUILT',
                'name' => [
                    'en' => 'Calculated built',
                ],
            ],
            [
                'type' => 'EPB_ASSESS_CALC_ACTUAL',
                'name' => [
                    'en' => 'Calculated actual',
                ],
            ],
            [
                'type' => 'EPB_ASSESS_CALC_TAILORED',
                'name' => [
                    'en' => 'Calculated tailored',
                ],
            ],
            [
                'type' => 'EPB_ASSESS_MEAS_ACTUAL',
                'name' => [
                    'en' => 'Measured actual',
                ],
            ],
            [
                'type' => 'EPB_ASSESS_MEAS_CORR_CLIM',
                'name' => [
                    'en' => 'Measured corrected climate',
                ],
            ],
            [
                'type' => 'EPB_ASSESS_MEAS_CORR_USE',
                'name' => [
                    'en' => 'Measured corrected for use',
                ],
            ],
            [
                'type' => 'EPB_ASSESS_MEAS_STAND',
                'name' => [
                    'en' => 'Measured standard',
                ],
            ],
        ];

        foreach ($types as $type) {
            \DB::table('assessment_types')->insert([
                'type' => $type['type'],
                'name' => json_encode($type['name']),
            ]);
        }
    }
}
