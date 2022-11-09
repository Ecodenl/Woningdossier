<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SpaceCategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = [
            [
                'type' => 'SPACECAT_RES_LIV',
                'name' => [
                    'en' => 'Residential living space, kitchen, bed room, study, bath room or toilet',
                ],
            ],
            [
                'type' => 'SPACECAT_RES_INDIV_OTHER',
                'name' => [
                    'en' => 'Residential individual: hall, corridor, staircase inside thermal envelope, attic inside thermal envelope',
                ],
            ],
            [
                'type' => 'SPACECAT_RES_COLL',
                'name' => [
                    'en' => 'Residential collective or non-residential: hall, corridor, staircase inside thermal envelope',
                ],
            ],
            [
                'type' => 'SPACECAT_TH.UNCOND_OTHER',
                'name' => [
                    'en' => 'Thermally unconditioned adjacent space, such as storage room or unconditioned attic',
                ],
            ],
            [
                'type' => 'SPACECAT_TH.UNCOND_SUN',
                'name' => [
                    'en' => 'Thermally unconditioned sunspace or atrium',
                ],
            ],
            [
                'type' => 'SPACECAT_HALL',
                'name' => [
                    'en' => 'Entrance hall/foyer',
                ],
            ],
            [
                'type' => 'SPACECAT_CORR',
                'name' => [
                    'en' => 'Corridor',
                ],
            ],
            [
                'type' => 'SPACECAT_TH.UNCOND_CORR',
                'name' => [
                    'en' => 'Hall, corridor outside thermal envelope',
                ],
            ],
            [
                'type' => 'SPACECAT_OFF',
                'name' => [
                    'en' => 'Office space',
                ],
            ],
            [
                'type' => 'SPACECAT_EDUC',
                'name' => [
                    'en' => 'Educational space',
                ],
            ],
            [
                'type' => 'SPACECAT_HOSP_BED',
                'name' => [
                    'en' => 'Hospital bed room',
                ],
            ],
            [
                'type' => 'SPACECAT_HOSP_OTHER',
                'name' => [
                    'en' => 'Hospital other room',
                ],
            ],
            [
                'type' => 'SPACECAT_HOTEL',
                'name' => [
                    'en' => 'Hotels room',
                ],
            ],
            [
                'type' => 'SPACECAT_REST',
                'name' => [
                    'en' => 'Restaurant space',
                ],
            ],
            [
                'type' => 'SPACECAT_REST_KITCH',
                'name' => [
                    'en' => 'Restaurant kitchen',
                ],
            ],
            [
                'type' => 'SPACECAT_MEET',
                'name' => [
                    'en' => 'Meeting or seminar space',
                ],
            ],
            [
                'type' => 'SPACECAT_AUDIT',
                'name' => [
                    'en' => 'Auditorium, lecture room',
                ],
            ],
            [
                'type' => 'SPACECAT_THEAT',
                'name' => [
                    'en' => 'Theatre or cinema space',
                ],
            ],
            [
                'type' => 'SPACECAT_SERVER',
                'name' => [
                    'en' => 'Server or computer room',
                ],
            ],
            [
                'type' => 'SPACECAT_SPORT_TH.COND',
                'name' => [
                    'en' => 'Sport facilities, thermally conditioned',
                ],
            ],
            [
                'type' => 'SPACECAT_SPORT_TH.UNCOND',
                'name' => [
                    'en' => 'Sport facilities, thermally unconditioned',
                ],
            ],
            [
                'type' => 'SPACECAT_RETAIL',
                'name' => [
                    'en' => 'Wholesale and retail trade services space (shop)',
                ],
            ],
            [
                'type' => 'SPACECAT_NONRES_BATH',
                'name' => [
                    'en' => 'Non-residential bath room, shower, toilet, if inside thermal envelope',
                ],
            ],
            [
                'type' => 'SPACECAT_SPA',
                'name' => [
                    'en' => 'Spa area with sauna shower and/or relaxing area',
                ],
            ],
            [
                'type' => 'SPACECAT_SWIMM',
                'name' => [
                    'en' => 'Space with indoor swimming pool',
                ],
            ],
            [
                'type' => 'SPACECAT_STOR_HEAT',
                'name' => [
                    'en' => 'Heated storage space',
                ],
            ],
            [
                'type' => 'SPACECAT_STOR_COOL',
                'name' => [
                    'en' => 'Cooled storage space',
                ],
            ],
            [
                'type' => 'SPACECAT_STOR_NOCON',
                'name' => [
                    'en' => 'Non conditioned storage space',
                ],
            ],
            [
                'type' => 'SPACECAT_ENGINE',
                'name' => [
                    'en' => 'Engine room',
                ],
            ],
            [
                'type' => 'SPACECAT_CAR',
                'name' => [
                    'en' => 'Individual garage or collective indoor car park',
                ],
            ],
            [
                'type' => 'SPACECAT_BARN',
                'name' => [
                    'en' => 'Barn',
                ],
            ],
        ];

        foreach ($categories as $category) {
            DB::table('space_categories')->insert([
                'type' => $category['type'],
                'name' => json_encode($category['name']),
            ]);
        }
    }
}
