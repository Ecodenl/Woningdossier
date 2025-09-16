<?php

namespace App\Helpers\Calculation;

use App\Models\BuildingHeating;
use App\Models\UserEnergyHabit;

class RoomTemperatureCalculator
{
    const string FLOOR_GROUND_ROOM_LIVING_ROOM = 'bg woonkamer';
    const string FLOOR_GROUND_ROOM_KITCHEN = 'bg keuken';
    const string FLOOR_GROUND_ROOM_HALL = 'bg gang';
    const string FLOOR_ONE_ROOM_BEDROOM1 = '1e v slaapkamer1';
    const string FLOOR_ONE_ROOM_BEDROOM2 = '1e v slaapkamer2';
    const string FLOOR_ONE_ROOM_HALL = '1e v gang';
    const string ROOM_BATHROOM = 'badkamer';
    const string ROOM_ATTIC = 'zolder';

    protected array $rooms = [
        self::FLOOR_GROUND_ROOM_LIVING_ROOM => [
            'm2' => 30,
            'temp high' => 0,
            'hours high' => 0,
            'temp low' => 0,
            'hours low' => 0,
            'average' => 0,
        ],
        self::FLOOR_GROUND_ROOM_KITCHEN => [
            'm2' => 10,
            'temp high' => 0,
            'hours high' => 0,
            'temp low' => 0,
            'hours low' => 0,
            'average' => 0,
        ],
        self::FLOOR_GROUND_ROOM_HALL => [
            'm2' => 10,
            'temp high' => 0,
            'hours high' => 0,
            'temp low' => 0,
            'hours low' => 0,
            'average' => 0,
        ],
        self::FLOOR_ONE_ROOM_BEDROOM1 => [
            'm2' => 30,
            'temp high' => 0,
            'hours high' => 0,
            'temp low' => 0,
            'hours low' => 0,
            'average' => 0,
        ],
        self::FLOOR_ONE_ROOM_BEDROOM2 => [
            'm2' => 10,
            'temp high' => 0,
            'hours high' => 0,
            'temp low' => 0,
            'hours low' => 0,
            'average' => 0,
        ],
        self::FLOOR_ONE_ROOM_HALL => [
            'm2' => 5,
            'temp high' => 0,
            'hours high' => 0,
            'temp low' => 0,
            'hours low' => 0,
            'average' => 0,
        ],
        self::ROOM_BATHROOM => [
            'm2' => 5,
            'temp high' => 0,
            'hours high' => 0,
            'temp low' => 0,
            'hours low' => 0,
            'average' => 0,
        ],
        self::ROOM_ATTIC => [
            'm2' => 20,
            'temp high' => 0,
            'hours high' => 0,
            'temp low' => 0,
            'hours low' => 0,
            'average' => 0,
        ],
    ];

    public function __construct(UserEnergyHabit $habits)
    {
        $firstFloorHeating = $habits->heatingFirstFloor;
        if (! $firstFloorHeating instanceof BuildingHeating) {
            $firstFloorHeating = BuildingHeating::where('is_default', true)->first();
        }

        $secondFloorHeating = $habits->heatingSecondFloor;
        if (! $secondFloorHeating instanceof BuildingHeating) {
            $secondFloorHeating = BuildingHeating::where('is_default', true)->first();
        }

        // new logic:
        // if the heating_(first/second)_floor is Not applicable (calculate_value 4):
        // set ALL m2 for that FLOOR to 0
        if (5 == $firstFloorHeating->calculate_value) {
            $firstFloorRooms = [
                self::FLOOR_ONE_ROOM_BEDROOM1,
                self::FLOOR_ONE_ROOM_BEDROOM2,
                self::FLOOR_ONE_ROOM_HALL,
                self::ROOM_BATHROOM,
            ];
            // \Log::debug('No heating on first floor, setting the following rooms to 0 m2: '.implode(', ', $firstFloorRooms));
            foreach ($firstFloorRooms as $firstFloorRoom) {
                $this->rooms[$firstFloorRoom]['m2'] = 0;
            }
        }
        if (5 == $secondFloorHeating->calculate_value) {
            $secondFloorRooms = [
                self::ROOM_ATTIC,
            ];
            // \Log::debug('No heating on second floor, setting the following rooms to 0 m2: '.implode(', ', $secondFloorRooms));
            foreach ($secondFloorRooms as $secondFloorRoom) {
                $this->rooms[$secondFloorRoom]['m2'] = 0;
            }
        }

        // living room
        $this->rooms[self::FLOOR_GROUND_ROOM_LIVING_ROOM]['temp high'] = $habits->thermostat_high;
        $this->rooms[self::FLOOR_GROUND_ROOM_LIVING_ROOM]['hours high'] = $habits->hours_high;
        $this->rooms[self::FLOOR_GROUND_ROOM_LIVING_ROOM]['temp low'] = $habits->thermostat_low;
        $this->rooms[self::FLOOR_GROUND_ROOM_LIVING_ROOM]['hours low'] = 24 - $this->rooms[self::FLOOR_GROUND_ROOM_LIVING_ROOM]['hours high'];
        $this->rooms[self::FLOOR_GROUND_ROOM_LIVING_ROOM]['average'] = $this->calculateAverage(self::FLOOR_GROUND_ROOM_LIVING_ROOM);

        // kitchen
        $this->rooms[self::FLOOR_GROUND_ROOM_KITCHEN]['temp high'] = $this->rooms[self::FLOOR_GROUND_ROOM_LIVING_ROOM]['temp high'];
        $this->rooms[self::FLOOR_GROUND_ROOM_KITCHEN]['hours high'] = $this->rooms[self::FLOOR_GROUND_ROOM_LIVING_ROOM]['hours high'];
        $this->rooms[self::FLOOR_GROUND_ROOM_KITCHEN]['temp low'] = $this->rooms[self::FLOOR_GROUND_ROOM_LIVING_ROOM]['temp low'];
        $this->rooms[self::FLOOR_GROUND_ROOM_KITCHEN]['hours low'] = $this->rooms[self::FLOOR_GROUND_ROOM_LIVING_ROOM]['hours low'];
        $this->rooms[self::FLOOR_GROUND_ROOM_KITCHEN]['average'] = $this->calculateAverage(self::FLOOR_GROUND_ROOM_KITCHEN);

        // bg hall
        $this->rooms[self::FLOOR_GROUND_ROOM_HALL]['temp high'] = $this->rooms[self::FLOOR_GROUND_ROOM_LIVING_ROOM]['temp high'] - 2;
        $this->rooms[self::FLOOR_GROUND_ROOM_HALL]['hours high'] = $this->rooms[self::FLOOR_GROUND_ROOM_LIVING_ROOM]['hours high'];
        $this->rooms[self::FLOOR_GROUND_ROOM_HALL]['temp low'] = $this->rooms[self::FLOOR_GROUND_ROOM_LIVING_ROOM]['temp low'] - 1;
        $this->rooms[self::FLOOR_GROUND_ROOM_HALL]['hours low'] = $this->rooms[self::FLOOR_GROUND_ROOM_LIVING_ROOM]['hours low'];
        $this->rooms[self::FLOOR_GROUND_ROOM_HALL]['average'] = $this->calculateAverage(self::FLOOR_GROUND_ROOM_HALL);

        // 1st fl bedroom1
        $this->rooms[self::FLOOR_ONE_ROOM_BEDROOM1]['temp high'] = $firstFloorHeating instanceof BuildingHeating ? $firstFloorHeating->degree : 10;
        $this->rooms[self::FLOOR_ONE_ROOM_BEDROOM1]['hours high'] = $this->rooms[self::FLOOR_GROUND_ROOM_LIVING_ROOM]['hours high'];
        $this->rooms[self::FLOOR_ONE_ROOM_BEDROOM1]['temp low'] = $this->calculateTempLow(self::FLOOR_ONE_ROOM_BEDROOM1);
        $this->rooms[self::FLOOR_ONE_ROOM_BEDROOM1]['hours low'] = $this->rooms[self::FLOOR_GROUND_ROOM_LIVING_ROOM]['hours low'];
        $this->rooms[self::FLOOR_ONE_ROOM_BEDROOM1]['average'] = $this->calculateAverage(self::FLOOR_ONE_ROOM_BEDROOM1);

        // 1st fl bedroom2
        $this->rooms[self::FLOOR_ONE_ROOM_BEDROOM2]['temp high'] = $this->rooms[self::FLOOR_ONE_ROOM_BEDROOM1]['temp high'];
        $this->rooms[self::FLOOR_ONE_ROOM_BEDROOM2]['hours high'] = $this->rooms[self::FLOOR_GROUND_ROOM_LIVING_ROOM]['hours high'];
        $this->rooms[self::FLOOR_ONE_ROOM_BEDROOM2]['temp low'] = $this->calculateTempLow(self::FLOOR_ONE_ROOM_BEDROOM2);
        $this->rooms[self::FLOOR_ONE_ROOM_BEDROOM2]['hours low'] = $this->rooms[self::FLOOR_GROUND_ROOM_LIVING_ROOM]['hours low'];
        $this->rooms[self::FLOOR_ONE_ROOM_BEDROOM2]['average'] = $this->calculateAverage(self::FLOOR_ONE_ROOM_BEDROOM2);

        // 1st fl hall
        $this->rooms[self::FLOOR_ONE_ROOM_HALL]['temp high'] = $this->rooms[self::FLOOR_ONE_ROOM_BEDROOM1]['temp high'];
        $this->rooms[self::FLOOR_ONE_ROOM_HALL]['hours high'] = $this->rooms[self::FLOOR_GROUND_ROOM_LIVING_ROOM]['hours high'];
        $this->rooms[self::FLOOR_ONE_ROOM_HALL]['temp low'] = $this->calculateTempLow(self::FLOOR_ONE_ROOM_HALL);
        $this->rooms[self::FLOOR_ONE_ROOM_HALL]['hours low'] = $this->rooms[self::FLOOR_GROUND_ROOM_LIVING_ROOM]['hours low'];
        $this->rooms[self::FLOOR_ONE_ROOM_HALL]['average'] = $this->calculateAverage(self::FLOOR_ONE_ROOM_HALL);

        // bathroom
        $this->rooms[self::ROOM_BATHROOM]['temp high'] = 20;
        $this->rooms[self::ROOM_BATHROOM]['hours high'] = $this->rooms[self::FLOOR_GROUND_ROOM_LIVING_ROOM]['hours high'];
        $this->rooms[self::ROOM_BATHROOM]['temp low'] = $this->rooms[self::FLOOR_GROUND_ROOM_LIVING_ROOM]['temp low'];
        $this->rooms[self::ROOM_BATHROOM]['hours low'] = $this->rooms[self::FLOOR_GROUND_ROOM_LIVING_ROOM]['hours low'];
        $this->rooms[self::ROOM_BATHROOM]['average'] = $this->calculateAverage(self::ROOM_BATHROOM);

        // attic
        $this->rooms[self::ROOM_ATTIC]['temp high'] = $secondFloorHeating instanceof BuildingHeating ? $secondFloorHeating->degree : 10;
        $this->rooms[self::ROOM_ATTIC]['hours high'] = $this->rooms[self::FLOOR_GROUND_ROOM_LIVING_ROOM]['hours high'];
        $this->rooms[self::ROOM_ATTIC]['temp low'] = $this->calculateTempLow(self::ROOM_ATTIC);
        $this->rooms[self::ROOM_ATTIC]['hours low'] = $this->rooms[self::FLOOR_GROUND_ROOM_LIVING_ROOM]['hours low'];
        $this->rooms[self::ROOM_ATTIC]['average'] = $this->calculateAverage(self::ROOM_ATTIC);
    }

    public function getAverageHouseTemperature()
    {
        $total = 0;
        $surface = 0;

        // \Log::debug(__METHOD__.' Rooms:');
        // \Log::debug(json_encode($this->rooms));

        foreach ($this->rooms as $room => $values) {
            $total += $values['m2'] * $values['average'];
            $surface += $values['m2'];
        }

        return number_format($total / $surface, 1);
    }

    protected function calculateTempLow($room)
    {
        if (18 == $this->rooms[$room]['temp high']) {
            return $this->rooms[self::FLOOR_GROUND_ROOM_LIVING_ROOM]['temp low'] - 2;
        }
        if (13 == $this->rooms[$room]['temp high']) {
            return 10;
        }

        return $this->rooms[$room]['temp high'];
    }

    protected function calculateAverage($room)
    {
        return (
            ($this->rooms[$room]['temp high'] * $this->rooms[$room]['hours high']) + ($this->rooms[$room]['temp low'] * $this->rooms[$room]['hours low'])
        ) / 24;
    }
}
