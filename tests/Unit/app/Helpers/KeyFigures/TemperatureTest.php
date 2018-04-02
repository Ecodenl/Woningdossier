<?php

namespace Tests\Unit\app\Helpers\KeyFigures;

use App\Helpers\KeyFigures\Temperature;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TemperatureTest extends TestCase
{

	public static function dataProvider(){
		return [
			[
				8.78, Temperature::WALL_INSULATION_JOINTS, 16.3,
			],
			[
				9.61, Temperature::WALL_INSULATION_FACADE, 16.3,
			],
			[
				8.78, Temperature::WALL_INSULATION_RESEARCH, 16.3,
			],
		];
	}

	/**
	 * @dataProvider dataProvider
	 */
	public function testEnergySavingFigureWallInsulation($expected, $measure, $averageHouseTemp){
		$this->assertEquals($expected, Temperature::energySavingFigureWallInsulation($measure, $averageHouseTemp));
	}
}
