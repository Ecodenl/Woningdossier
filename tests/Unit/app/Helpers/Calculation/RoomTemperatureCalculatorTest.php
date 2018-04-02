<?php

namespace Tests\Unit\app\Helpers\Calculation;

use App\Helpers\Calculation\RoomTemperatureCalculator;
use App\Models\BuildingHeating;
use App\Models\UserEnergyHabit;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RoomTemperatureCalculatorTest extends TestCase
{
    public function testGetAverageHouseTemperature(){
		$habits = new UserEnergyHabit([
			'thermostat_high' => 20,
			'thermostat_low' => 16,
			'hours_high' => 12,
		]);
		$firstFloor = new BuildingHeating();
		$firstFloor->degree = 18; // Verwarmd, de meeste radiatoren staan aan
		$secondFloor = new BuildingHeating();
		$secondFloor->degree = 13; // Matig verwarmd, de meeste radiatoren saan hoger van * of een aantal radiatoren staan hoger

		$habits->heatingFirstFloor()->associate($firstFloor);
		$habits->heatingSecondFloor()->associate($secondFloor);

		$calculator = new RoomTemperatureCalculator($habits);
		$this->assertEquals(16.0, $calculator->getAverageHouseTemperature());
    }
}
