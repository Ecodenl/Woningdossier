<?php

use Illuminate\Database\Seeder;

class HeaterComponentCostsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		$costs = [
			[
				'component' => 'boiler',
				'size' => 120,
				'cost' => 2000,
			],
			[
				'component' => 'boiler',
				'size' => 200,
				'cost' => 2400,
			],
			[
				'component' => 'boiler',
				'size' => 300,
				'cost' => 3200,
			],
			[
				'component' => 'boiler',
				'size' => 400,
				'cost' => 3800,
			],

			[
				'component' => 'collector',
				'size' => 1.6,
				'cost' => 500,
			],
			[
				'component' => 'collector',
				'size' => 2.5,
				'cost' => 725,
			],
			[
				'component' => 'collector',
				'size' => 3.2,
				'cost' => 950,
			],
			[
				'component' => 'collector',
				'size' => 4.8,
				'cost' => 1600,
			],
			[
				'component' => 'collector',
				'size' => 5,
				'cost' => 1700,
			],
			[
				'component' => 'collector',
				'size' => 6.4,
				'cost' => 1900,
			],
			[
				'component' => 'collector',
				'size' => 7.5,
				'cost' => 2200,
			],
			[
				'component' => 'collector',
				'size' => 10,
				'cost' => 2900,
			],
		];

		\DB::table('heater_component_costs')->insert($costs);
    }
}
