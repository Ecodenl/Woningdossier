<?php

use Illuminate\Database\Seeder;

class ExampleBuildingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $translationPrefix = 'woningdossier.cooperation.tool.example-buildings.';

        $exampleBuildings = [
			[
				'translation_key' => $translationPrefix .'woning0',
				'order' => 99,
			],
	        [
		        'translation_key' => $translationPrefix .'woning1',
		        'order' => 0,
	        ],
	        [
		        'translation_key' => $translationPrefix .'woning2',
		        'order' => 1,
	        ],
	        [
		        'translation_key' => $translationPrefix .'woning3',
		        'order' => 2,
	        ],
	        [
		        'translation_key' => $translationPrefix .'woning4',
		        'order' => 3,
	        ],
	        [
		        'translation_key' => $translationPrefix .'woning5',
		        'order' => 6,
	        ],
	        [
		        'translation_key' => $translationPrefix .'woning6',
		        'order' => 7,
	        ],
	        [
		        'translation_key' => $translationPrefix .'woning7',
		        'order' => 8,
	        ],
	        [
		        'translation_key' => $translationPrefix .'woning8',
		        'order' => 9,
	        ],
	        [
		        'translation_key' => $translationPrefix .'woning9',
		        'order' => 10,
	        ],
	        [
		        'translation_key' => $translationPrefix .'woning10',
		        'order' => 11,
	        ],
	        [
		        'translation_key' => $translationPrefix .'woning11',
		        'order' => 12,
	        ],
	        [
		        'translation_key' => $translationPrefix .'woning12',
		        'order' => 13,
	        ],
	        [
		        'translation_key' => $translationPrefix .'woning13',
		        'order' => 4,
	        ],
	        [
		        'translation_key' => $translationPrefix .'woning14',
		        'order' => 5,
	        ],
        ];

        DB::table('example_buildings')->insert($exampleBuildings);

    }
}
