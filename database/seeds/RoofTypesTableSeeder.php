<?php

use Illuminate\Database\Seeder;

class RoofTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
	    $translationPrefix = 'woningdossier.cooperation.tool.roof-types.';

        $roofTypes = [
        	[
        		'translation_key' => $translationPrefix . 'type0',
		        'order' => 0,
		        'calculate_value' => 1,
	        ],
	        [
		        'translation_key' => $translationPrefix . 'type1',
		        'order' => 1,
		        'calculate_value' => 1,
	        ],
	        [
		        'translation_key' => $translationPrefix . 'type2',
		        'order' => 2,
		        'calculate_value' => 2,
	        ],
	        [
		        'translation_key' => $translationPrefix . 'type3',
		        'order' => 3,
		        'calculate_value' => 2,
	        ],
        ];

        foreach($roofTypes as $roofType){
        	DB::table('roof_types')->insert($roofType);
        }
    }
}
