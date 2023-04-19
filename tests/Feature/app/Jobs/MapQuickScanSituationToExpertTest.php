<?php

namespace Tests\Feature\app\Jobs;

use App\Helpers\Cooperation\Tool\HeatPumpHelper;
use App\Helpers\RoleHelper;
use App\Jobs\MapQuickScanSituationToExpert;
use App\Models\ComfortLevelTapWater;
use App\Models\InputSource;
use App\Models\MeasureApplication;
use App\Models\Role;
use App\Models\Service;
use App\Models\ToolQuestion;
use App\Models\User;
use App\Services\ToolQuestionService;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class MapQuickScanSituationToExpertTest extends TestCase
{
    use WithFaker,
        RefreshDatabase;

    public $seed = true;
    public $seeder = DatabaseSeeder::class;

    public function test_job_maps_all_cases_correctly()
    {
        // One user is seeded, we'll just use it.
        $user = User::first();
        $user->assignRole(Role::findByName(RoleHelper::ROLE_RESIDENT));
        $building = $user->building;

        $inputSource = InputSource::findByShort(InputSource::RESIDENT_SHORT);

        $heatPumpType = Service::findByShort('heat-pump')->values()->orderBy('calculate_value')->first()->id;
        $advisable = MeasureApplication::findByShort(array_flip(HeatPumpHelper::MEASURE_SERVICE_LINK)[$heatPumpType]);

        // First define basic mapping.
        $answersToSave = [
            'heat-source' => ['heat-pump'],
            'heat-pump-type' => $heatPumpType,
            'boiler-setting-comfort-heat' => 'temp-high',
            'building-heating-application' => ['radiators', 'floor-heating', 'low-temperature-heater'],
            'cook-type' => 'gas',
            'water-comfort' => ComfortLevelTapWater::where('calculate_value', 1)->first()->id,
        ];

        foreach ($answersToSave as $short => $answer) {
            ToolQuestionService::init(
                ToolQuestion::findByShort($short)
            )->building($building)
                ->currentInputSource($inputSource)
                ->save($answer);
        }

        MapQuickScanSituationToExpert::dispatchSync(
            $building,
            $inputSource,
            $advisable
        );

        $answersExpected = [
            'new-heat-pump-type' => $advisable->short,
            'new-heat-source' => ['hr-boiler', 'heat-pump'],
            'new-heat-source-warm-tap-water' => ['hr-boiler'],
            'heat-pump-replace' => true,
            'hr-boiler-replace' => true,//null, // No placing year currently
            'new-building-heating-application' => ['radiators', 'floor-heating', 'low-temperature-heater'],
            'new-water-comfort' => 'standard',
            'new-cook-type' => 'gas',
            'new-boiler-type' => 'hr107',
            'new-boiler-setting-comfort-heat' => 'temp-high',
        ];

        foreach ($answersExpected as $short => $answer) {
            $this->assertEquals(
                $answer,
                $building->getAnswer($inputSource, ToolQuestion::findByShort($short))
            );
        }

        // Boiler type is not viewable, so ensure it's handled as "null" if boiler type isn't in the heat source.
        $boilerValues = Service::findByShort('boiler')->values;

        ToolQuestionService::init(
            ToolQuestion::findByShort('boiler-type')
        )->building($building)
            ->currentInputSource($inputSource)
            ->save($boilerValues->first()->id);

        MapQuickScanSituationToExpert::dispatchSync(
            $building,
            $inputSource,
            $advisable
        );

        $this->assertEquals('hr107', $building->getAnswer($inputSource, ToolQuestion::findByShort('new-boiler-type')));


        // Retry mapping with all possible boiler types, after adding to heat-source.
        ToolQuestionService::init(
            ToolQuestion::findByShort('heat-source')
        )->building($building)
            ->currentInputSource($inputSource)
            ->save(['hr-boiler', 'heat-pump']);

        foreach ($boilerValues as $boilerValue) {
            ToolQuestionService::init(
                ToolQuestion::findByShort('boiler-type')
            )->building($building)
                ->currentInputSource($inputSource)
                ->save($boilerValue->id);

            MapQuickScanSituationToExpert::dispatchSync(
                $building,
                $inputSource,
                $advisable
            );

            $answersExpected['new-boiler-type'] = ToolQuestion::findByShort('new-boiler-type')
                ->toolQuestionCustomValues()
                ->where('extra->calculate_value', $boilerValue->calculate_value)
                ->first()
                ->short;

            foreach ($answersExpected as $short => $answer) {
                $this->assertEquals(
                    $answer,
                    $building->getAnswer($inputSource, ToolQuestion::findByShort($short))
                );
            }
        }

        // Change advisable to non-hybrid.
        $heatPumpType = Service::findByShort('heat-pump')->values()->orderByDesc('calculate_value')->first()->id;
        $advisable = MeasureApplication::findByShort(array_flip(HeatPumpHelper::MEASURE_SERVICE_LINK)[$heatPumpType]);

        ToolQuestionService::init(
            ToolQuestion::findByShort('heat-pump-type')
        )->building($building)
            ->currentInputSource($inputSource)
            ->save($heatPumpType);

        MapQuickScanSituationToExpert::dispatchSync(
            $building,
            $inputSource,
            $advisable
        );

        $answersExpected['new-heat-pump-type'] = $advisable->short;
        $answersExpected['new-heat-source'] = ['heat-pump'];
        $answersExpected['new-heat-source-warm-tap-water'] = ['heat-pump'];

        foreach ($answersExpected as $short => $answer) {
            $this->assertEquals(
                $answer,
                $building->getAnswer($inputSource, ToolQuestion::findByShort($short))
            );
        }

        // Change mapping just once more.
        $updateAnswers = [
            'building-heating-application' => ['wall-heating', 'air-heating', 'none'],
            'cook-type' => 'electric',
            'water-comfort' => ComfortLevelTapWater::where('calculate_value', 3)->first()->id,
        ];

        foreach ($updateAnswers as $short => $answer) {
            ToolQuestionService::init(
                ToolQuestion::findByShort($short)
            )->building($building)
                ->currentInputSource($inputSource)
                ->save($answer);
        }

        MapQuickScanSituationToExpert::dispatchSync(
            $building,
            $inputSource,
            $advisable
        );

        $answersExpected['new-building-heating-application'] = ['wall-heating', 'air-heating'];
        $answersExpected['new-cook-type'] = 'electric';
        $answersExpected['new-water-comfort'] = 'extra-comfortable';

        foreach ($answersExpected as $short => $answer) {
            $this->assertEquals(
                $answer,
                $building->getAnswer($inputSource, ToolQuestion::findByShort($short))
            );
        }
    }
}