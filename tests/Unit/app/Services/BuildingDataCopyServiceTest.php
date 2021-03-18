<?php

namespace Tests\Unit\app\Services;

use App\Models\MeasureApplication;
use App\Models\Step;
use App\Services\BuildingDataCopyService;
use Tests\CreatesApplication;
use Tests\TestCase;

class BuildingDataCopyServiceTest extends TestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();
    }


    public function makeTargetsValuesProvider()
    {
        return [
            [
                (object)['interested_in_type' => Step::class, 'interested_in_id' => 5, 'interest_id' => 3],
                [
                    (object)['interested_in_type' => Step::class, 'interested_in_id' => 5, 'interest_id' => 2],
                ],
                'interested_in_type',
                'interested_in_id',
                (object)['interested_in_type' => Step::class, 'interested_in_id' => 5, 'interest_id' => 2],
            ],
            [
                (object)['service_id' => 3, 'service_value_id' => 24, 'extra' => ['year' => 2008]],
                [
                    (object)['service_id' => 3, 'service_value_id' => 22],
                    (object)['service_id' => 3, 'service_value_id' => 23]
                ],
                'service_id',
                'service_value_id',
                null
            ],
            [
                (object)['step_id' => 3, 'measure_application_id' => 4, 'costs' => 750, 'savings_gas' => 0.0],
                [
                    (object)['step_id' => 5, 'measure_application_id' => 18, 'costs' => 650, 'savings_gas' => 29],
                    (object)['step_id' => 2, 'measure_application_id' => 24, 'costs' => 7921, 'savings_gas' => 78],
                    (object)['step_id' => 1, 'measure_application_id' => 12, 'costs' => 1950, 'savings_gas' => 12],
                    (object)['step_id' => 1, 'measure_application_id' => 11, 'costs' => 2500, 'savings_gas' => 56],
                    (object)['step_id' => 3, 'measure_application_id' => 2, 'costs' => 110, 'savings_gas' => 45],
                    (object)['step_id' => 3, 'measure_application_id' => 4, 'costs' => 2190, 'savings_gas' => 23],
                ],
                'step_id',
                'measure_application_id',
                (object)['step_id' => 3, 'measure_application_id' => 4, 'costs' => 2190, 'savings_gas' => 23]
            ],
            [
                (object)['step_id' => 5, 'measure_application_id' => 1, 'costs' => 610, 'savings_gas' => 0.0],
                [
                ],
                'step_id',
                'measure_application_id',
                null
            ],
            [
                (object)['interested_in_type' => MeasureApplication::class, 'interested_in_id' => 5, 'interest_id' => 3],
                [
                    (object)['interested_in_type' => MeasureApplication::class, 'interested_in_id' => 5, 'interest_id' => 2],
                    (object)['interested_in_type' => Step::class, 'interested_in_id' => 5, 'interest_id' => 2],
                ],
                'interested_in_type',
                'interested_in_id',
                (object)['interested_in_type' => MeasureApplication::class, 'interested_in_id' => 5, 'interest_id' => 2],
            ],
            [
                (object)['interested_in_type' => MeasureApplication::class, 'interested_in_id' => 5, 'interest_id' => 3],
                [
                    (object)['interested_in_type' => MeasureApplication::class, 'interested_in_id' => 5, 'interest_id' => 2],
                    (object)['interested_in_type' => Step::class, 'interested_in_id' => 5, 'interest_id' => 2],
                ],
                'interested_in_type',
                'interested_in_id',
                (object)['interested_in_type' => MeasureApplication::class, 'interested_in_id' => 5, 'interest_id' => 2],
            ],
            [
                (object)['element_id' => 3, 'element_value_id' => 36],
                [
                    (object)['element_id' => 8, 'element_value_id' => 37],
                    (object)['element_id' => 5, 'element_value_id' => 25],
                    (object)['element_id' => 3, 'element_value_id' => 35],
                    (object)['element_id' => 8, 'element_value_id' => 36],
                ],
                'element_id',
                'element_value_id',
                null
            ],
            [
                (object)['element_id' => 8, 'element_value_id' => 36, 'extra' => ['content' => 'Which does not exists']],
                [
                    (object)['element_id' => 8, 'element_value_id' => 35],
                    (object)['element_id' => 8, 'element_value_id' => 36],
                ],
                'element_id',
                'element_value_id',
                (object)['element_id' => 8, 'element_value_id' => 36],
            ],
            [
                (object)['element_id' => 8, 'element_value_id' => 36],
                [
                    (object)['element_id' => 8, 'element_value_id' => 35],
                    (object)['element_id' => 8, 'element_value_id' => 37],
                ],
                'element_id',
                'element_value_id',
                null
            ],
            [
                (object)['element_id' => 8, 'element_value_id' => 37, 'extra' => ['year' => 2019]],
                [
                    (object)['element_id' => 8, 'element_value_id' => 37],
                ],
                'element_id',
                'element_value_id',
                (object)['element_id' => 8, 'element_value_id' => 37],
            ],
        ];
    }

    /**
     * @dataProvider makeTargetsValuesProvider
     */
    public function testMakeTargetValues($sourceValue, $targetValues, $whereColumn, $additionalWhereColumn, $expected)
    {
        $possibleTargetValue = BuildingDataCopyService::getPossibleTargetValues(
            $sourceValue,
            collect($targetValues),
            $whereColumn,
            $additionalWhereColumn
        );

        $this->assertEquals($expected, $possibleTargetValue);
    }


}
