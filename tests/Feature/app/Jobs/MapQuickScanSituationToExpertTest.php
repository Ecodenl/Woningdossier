<?php

namespace Tests\Feature\app\Jobs;

use App\Helpers\Cooperation\Tool\HeatPumpHelper;
use App\Helpers\RoleHelper;
use App\Jobs\MapQuickScanSituationToExpert;
use App\Models\Building;
use App\Models\ComfortLevelTapWater;
use App\Models\InputSource;
use App\Models\MeasureApplication;
use App\Models\Role;
use App\Models\Service;
use App\Models\ToolQuestion;
use App\Models\User;
use App\Services\ToolQuestionService;
use Carbon\Carbon;
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

    protected Building $building;
    protected InputSource $inputSource;

    public function test_job_maps_all_cases_correctly()
    {
        // One user is seeded, we'll just use it.
        $user = User::first();
        $user->assignRole(Role::findByName(RoleHelper::ROLE_RESIDENT));
        $building = $user->building;
        $this->building = $building;

        $inputSource = InputSource::findByShort(InputSource::RESIDENT_SHORT);
        $this->inputSource = $inputSource;

        $heatPumpType = Service::findByShort('heat-pump')->values()->orderBy('calculate_value')->first()->id;
        $advisable = MeasureApplication::findByShort(array_flip(HeatPumpHelper::MEASURE_SERVICE_LINK)[$heatPumpType]);

        // First define basic mapping.
        $answersToSave = [
            'heat-source' => ['heat-pump'],
            'heat-source-warm-tap-water' => ['hr-boiler'],
            'heat-pump-type' => $heatPumpType,
            'boiler-setting-comfort-heat' => 'temp-high',
            'building-heating-application' => ['radiators', 'floor-heating', 'low-temperature-heater'],
            'cook-type' => 'gas',
            'water-comfort' => ComfortLevelTapWater::where('calculate_value', 1)->first()->id,
        ];

        foreach ($answersToSave as $short => $answer) {
            app(ToolQuestionService::class)
                ->toolQuestion(ToolQuestion::findByShort($short))
                ->building($building)
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
            'hr-boiler-replace' => null, // No placing year currently
            'new-building-heating-application' => ['radiators', 'floor-heating', 'low-temperature-heater'],
            'new-water-comfort' => 'standard',
            'new-cook-type' => 'gas',
            'new-boiler-type' => 'hr107',
            'new-boiler-setting-comfort-heat' => 'temp-high',
        ];

        $this->checkAnswers($answersExpected);

        // Check warm water is properly copied (and none is mapped to hr-boiler).
        $heatSourceWarmTapWaterCases = [
            [['sun-boiler'], ['sun-boiler']],
            [['sun-boiler', 'none'], ['sun-boiler', 'hr-boiler']],
            [['heat-pump'], ['heat-pump']],
            [['district-heating'], ['district-heating']],
            [['electric-boiler', 'sun-boiler'], ['electric-boiler', 'sun-boiler']],
            [['none'], ['hr-boiler']],
        ];

        foreach ($heatSourceWarmTapWaterCases as $case) {
            $answersExpected['new-heat-source-warm-tap-water'] = $case[1];

            app(ToolQuestionService::class)
                ->toolQuestion(ToolQuestion::findByShort('heat-source-warm-tap-water'))
                ->building($building)
                ->currentInputSource($inputSource)
                ->save($case[0]);

            MapQuickScanSituationToExpert::dispatchSync(
                $building,
                $inputSource,
                $advisable
            );

            $this->checkAnswers($answersExpected);
        }

        // Boiler type is not viewable, so ensure it's handled as "null" if boiler type isn't in the heat source.
        $boilerValues = Service::findByShort('boiler')->values;

        app(ToolQuestionService::class)
            ->toolQuestion(ToolQuestion::findByShort('boiler-type'))
            ->building($building)
            ->currentInputSource($inputSource)
            ->save($boilerValues->first()->id);

        MapQuickScanSituationToExpert::dispatchSync(
            $building,
            $inputSource,
            $advisable
        );

        $this->checkAnswers($answersExpected);

        // Retry mapping with all possible boiler types, after adding to heat-source.
        app(ToolQuestionService::class)
            ->toolQuestion(ToolQuestion::findByShort('heat-source'))
            ->building($building)
            ->currentInputSource($inputSource)
            ->save(['hr-boiler', 'heat-pump']);

        foreach ($boilerValues as $boilerValue) {
            app(ToolQuestionService::class)
                ->toolQuestion(ToolQuestion::findByShort('boiler-type'))
                ->building($building)
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

            $this->checkAnswers($answersExpected);
        }

        // Change advisable to non-hybrid.
        $heatPumpType = Service::findByShort('heat-pump')->values()->orderByDesc('calculate_value')->first()->id;
        $advisable = MeasureApplication::findByShort(array_flip(HeatPumpHelper::MEASURE_SERVICE_LINK)[$heatPumpType]);

        app(ToolQuestionService::class)
            ->toolQuestion(ToolQuestion::findByShort('heat-pump-type'))
            ->building($building)
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

        $this->checkAnswers($answersExpected);

        // Test them again and see how they all map to heat-pump
        foreach ($heatSourceWarmTapWaterCases as $case) {
            app(ToolQuestionService::class)
                ->toolQuestion(ToolQuestion::findByShort('heat-source-warm-tap-water'))
                ->building($building)
                ->currentInputSource($inputSource)
                ->save($case[0]);

            MapQuickScanSituationToExpert::dispatchSync(
                $building,
                $inputSource,
                $advisable
            );

            $this->checkAnswers($answersExpected);
        }

        // Change mapping just once more.
        $updateAnswers = [
            'building-heating-application' => ['wall-heating', 'air-heating', 'none'],
            'cook-type' => 'electric',
            'water-comfort' => ComfortLevelTapWater::where('calculate_value', 3)->first()->id,
        ];

        foreach ($updateAnswers as $short => $answer) {
            app(ToolQuestionService::class)
                ->toolQuestion(ToolQuestion::findByShort($short))
                ->building($building)
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

        $this->checkAnswers($answersExpected);

        // Add placing year.
        $year = (int) Carbon::now()->format('Y');

        app(ToolQuestionService::class)
            ->toolQuestion(ToolQuestion::findByShort('boiler-placed-date'))
            ->building($building)
            ->currentInputSource($inputSource)
            ->save(($year - 9)); // Younger than 10 years, so should be no

        MapQuickScanSituationToExpert::dispatchSync(
            $building,
            $inputSource,
            $advisable
        );

        $answersExpected['hr-boiler-replace'] = false;

        $this->checkAnswers($answersExpected);

        app(ToolQuestionService::class)
            ->toolQuestion(ToolQuestion::findByShort('boiler-placed-date'))
            ->building($building)
            ->currentInputSource($inputSource)
            ->save(($year - 10)); // Older or equal to 10 years, so should be yes

        MapQuickScanSituationToExpert::dispatchSync(
            $building,
            $inputSource,
            $advisable
        );

        $answersExpected['hr-boiler-replace'] = true;

        $this->checkAnswers($answersExpected);
    }

    private function checkAnswers(array $answersExpected)
    {
        foreach ($answersExpected as $short => $answer) {
            $this->assertEquals(
                $answer,
                $this->building->getAnswer($this->inputSource, ToolQuestion::findByShort($short))
            );
        }
    }
}