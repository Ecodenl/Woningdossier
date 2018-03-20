<?php

use Illuminate\Database\Seeder;

class InterestsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
	    $translationPrefix = 'woningdossier.cooperation.tool.interests.';

        $interests = [
			[
				'translation_key' => $translationPrefix . 'interest0',
				'calculate_value' => 1,
			],
	        [
		        'translation_key' => $translationPrefix . 'interest1',
		        'calculate_value' => 2,
	        ],
	        [
		        'translation_key' => $translationPrefix . 'interest2',
		        'calculate_value' => 3,
	        ],
	        [
		        'translation_key' => $translationPrefix . 'interest3',
		        'calculate_value' => 4,
	        ],
	        [
		        'translation_key' => $translationPrefix . 'interest4',
		        'calculate_value' => 5,
	        ],
        ];

	    DB::table('interests')->insert($interests);
    }
}
