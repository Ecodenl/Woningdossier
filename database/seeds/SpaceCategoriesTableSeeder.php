<?php

use Illuminate\Database\Seeder;

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
			    'names' => [
				    'en' => 'Residential living space, kitchen, bed room, study, bath room or toilet',
			    ],
		    ],
		    [
			    'type' => 'SPACECAT_RES_INDIV_OTHER',
			    'names' => [
				    'en' => 'Residential individual: hall, corridor, staircase inside thermal envelope, attic inside thermal envelope',
			    ],
		    ],
		    [
			    'type' => 'SPACECAT_RES_COLL',
			    'names' => [
				    'en' => 'Residential collective or non-residential: hall, corridor, staircase inside thermal envelope',
			    ],
		    ],
		    [
			    'type' => 'SPACECAT_TH.UNCOND_OTHER',
			    'names' => [
				    'en' => 'Thermally unconditioned adjacent space, such as storage room or unconditioned attic',
			    ],
		    ],
		    [
			    'type' => 'SPACECAT_TH.UNCOND_SUN',
			    'names' => [
				    'en' => 'Thermally unconditioned sunspace or atrium',
			    ],
		    ],
		    [
			    'type' => 'SPACECAT_HALL',
			    'names' => [
				    'en' => 'Entrance hall/foyer',
			    ],
		    ],
		    [
			    'type' => 'SPACECAT_CORR',
			    'names' => [
				    'en' => 'Corridor',
			    ],
		    ],
		    [
			    'type' => 'SPACECAT_TH.UNCOND_CORR',
			    'names' => [
				    'en' => 'Hall, corridor outside thermal envelope',
			    ],
		    ],
		    [
			    'type' => 'SPACECAT_OFF',
			    'names' => [
				    'en' => 'Office space',
			    ],
		    ],
		    [
			    'type' => 'SPACECAT_EDUC',
			    'names' => [
				    'en' => 'Educational space',
			    ],
		    ],
		    [
			    'type' => 'SPACECAT_HOSP_BED',
			    'names' => [
				    'en' => 'Hospital bed room',
			    ],
		    ],
		    [
			    'type' => 'SPACECAT_HOSP_OTHER',
			    'names' => [
				    'en' => 'Hospital other room',
			    ],
		    ],
		    [
			    'type' => 'SPACECAT_HOTEL',
			    'names' => [
				    'en' => 'Hotels room',
			    ],
		    ],
		    [
			    'type' => 'SPACECAT_REST',
			    'names' => [
				    'en' => 'Restaurant space',
			    ],
		    ],
		    [
			    'type' => 'SPACECAT_REST_KITCH',
			    'names' => [
				    'en' => 'Restaurant kitchen',
			    ],
		    ],
		    [
			    'type' => 'SPACECAT_MEET',
			    'names' => [
				    'en' => 'Meeting or seminar space',
			    ],
		    ],
		    [
			    'type' => 'SPACECAT_AUDIT',
			    'names' => [
				    'en' => 'Auditorium, lecture room',
			    ],
		    ],
		    [
			    'type' => 'SPACECAT_THEAT',
			    'names' => [
				    'en' => 'Theatre or cinema space',
			    ],
		    ],
		    [
			    'type' => 'SPACECAT_SERVER',
			    'names' => [
				    'en' => 'Server or computer room',
			    ],
		    ],
		    [
			    'type' => 'SPACECAT_SPORT_TH.COND',
			    'names' => [
				    'en' => 'Sport facilities, thermally conditioned',
			    ],
		    ],
		    [
			    'type' => 'SPACECAT_SPORT_TH.UNCOND',
			    'names' => [
				    'en' => 'Sport facilities, thermally unconditioned',
			    ],
		    ],
		    [
			    'type' => 'SPACECAT_RETAIL',
			    'names' => [
				    'en' => 'Wholesale and retail trade services space (shop)',
			    ],
		    ],
		    [
			    'type' => 'SPACECAT_NONRES_BATH',
			    'names' => [
				    'en' => 'Non-residential bath room, shower, toilet, if inside thermal envelope',
			    ],
		    ],
		    [
			    'type' => 'SPACECAT_SPA',
			    'names' => [
				    'en' => 'Spa area with sauna shower and/or relaxing area',
			    ],
		    ],
		    [
			    'type' => 'SPACECAT_SWIMM',
			    'names' => [
				    'en' => 'Space with indoor swimming pool',
			    ],
		    ],
		    [
			    'type' => 'SPACECAT_STOR_HEAT',
			    'names' => [
				    'en' => 'Heated storage space',
			    ],
		    ],
		    [
			    'type' => 'SPACECAT_STOR_COOL',
			    'names' => [
				    'en' => 'Cooled storage space',
			    ],
		    ],
		    [
			    'type' => 'SPACECAT_STOR_NOCON',
			    'names' => [
				    'en' => 'Non conditioned storage space',
			    ],
		    ],
		    [
			    'type' => 'SPACECAT_ENGINE',
			    'names' => [
				    'en' => 'Engine room',
			    ],
		    ],
		    [
			    'type' => 'SPACECAT_CAR',
			    'names' => [
				    'en' => 'Individual garage or collective indoor car park',
			    ],
		    ],
		    [
			    'type' => 'SPACECAT_BARN',
			    'names' => [
				    'en' => 'Barn',
			    ],
		    ],
	    ];

	    foreach ($categories as $category) {
		    $uuid = \App\Helpers\Str::uuid();
		    foreach($category['names'] as $locale => $name) {
			    \DB::table( 'translations' )->insert( [
				    'key'         => $uuid,
				    'language'    => $locale,
				    'translation' => $name,
			    ] );
		    }

		    \DB::table('space_categories')->insert([
			    'type' => $category['type'],
			    'name' => $uuid,
		    ]);
	    }
    }
}
