<?php

namespace App\Console\Commands\Upgrade;

use App\Models\Building;
use App\Models\BuildingFeature;
use App\Models\BuildingService;
use App\Models\InputSource;
use App\Models\ServiceValue;
use App\Models\ToolQuestion;
use App\Models\ToolQuestionCustomValue;
use App\Models\User;
use App\Models\UserEnergyHabit;
use App\ToolQuestionAnswer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MapAnswers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'upgrade:map-answers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will map current data to new formats, eg; cook_gas was a boolean will now be a electric, induction or gas field ';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // keep in mind that the order of this map is important!
        $this->info('Cook gas field to the tool question answers...');
        $this->mapUserEnergyHabits();
        $this->info("Mapping the user motivations to the welke zaken vind u belangrijke rating slider style...");
        $this->mapUserMotivations();
        $this->info('Mapping building heating applications from building features to tool question building heating application');
        $this->mapBuildingFeatureBuildingHeatingToBuildingHeatingApplicationToolQuestion();
        $this->info('Mapping hr-boiler and heat-pump service to heat-source tool question...');
        $this->mapHrBoilerAndHeatPumpToHeatSourceToolQuestion();
        $this->info('Mapping boiler placed date (for users who haven\'t defined one)');
        $this->mapHrBoilerPlacedDate();
    }

    public function mapHrBoilerPlacedDate()
    {
        // this method will add a placed date for the boiler.
        $buildingServicesBoiler = BuildingService::allInputSources()
            ->with('building', 'inputSource')
            ->leftJoin('services as s', 'building_services.service_id', '=', 's.id')
            ->where('s.short', 'boiler')
            ->get(['building_services.*']);

        $date = null;
        $year = date('Y');
        $hrBoilerMap = [
            'Aanwezig, recent vervangen' => $year - 2,
            'Aanwezig, tussen 6 en 13 jaar oud' => $year - 10,
            'Aanwezig, ouder dan 13 jaar' => $year - 13,
        ];
        $bar = $this->output->createProgressBar($buildingServicesBoiler->count());

        $bar->start();
        foreach ($buildingServicesBoiler as $buildingServiceBoiler) {
            // now get the hr-boiler, this way we can try do determine the placed date for the user

            /** @var BuildingService $hrBoiler */
            $hrBoiler = $buildingServiceBoiler->building->buildingServices()->forInputSource($buildingServiceBoiler->inputSource)
                ->leftJoin('services as s', 'building_services.service_id', '=', 's.id')
                ->where('s.short', 'hr-boiler')
                ->first(['building_services.*']);

            if (
                $hrBoiler instanceof BuildingService &&
                $hrBoiler->serviceValue instanceof ServiceValue && (
                    !isset($buildingServiceBoiler->extra['date']) || empty($buildingServiceBoiler->extra['date'])
                )
            ) {
                $buildingServiceBoiler->update(['extra' => ['date' => $hrBoilerMap[$hrBoiler->serviceValue->value] ?? null]]);
            }
            $bar->advance();
        }

        $bar->finish();
        $this->output->newLine();
    }

    private function mapHrBoilerAndHeatPumpToHeatSourceToolQuestion()
    {
        $toolQuestion = ToolQuestion::findByShort('heat-source');
        $data = ['tool_question_id' => $toolQuestion->id];
        $buildings = Building::all();

        // the heat pump will actually handle the "onbekend" and "niet aanwezig" cases
        $hrBoilerMap = [
            'Aanwezig, recent vervangen' => 'hr-boiler',
            'Aanwezig, tussen 6 en 13 jaar oud' => 'hr-boiler',
            'Aanwezig, ouder dan 13 jaar' => 'hr-boiler',
        ];

        $heatPumpMap = [
            2 => ['heat-pump'],
            3 => ['heat-pump'],
            4 => ['heat-pump', 'hr-boiler'],
            5 => ['heat-pump']
        ];
        /** @var Building $building */
        $bar = $this->output->createProgressBar($buildings->count());
        foreach ($buildings as $building) {
            $bar->advance();
            $data['building_id'] = $building->id;
            // first handle the hr-boiler
            $buildingServicesHrBoiler = $building
                ->buildingServices()
                ->allInputSources()
                ->leftJoin('services as s', 'building_services.service_id', '=', 's.id')
                ->where('s.short', 'hr-boiler')->get(['building_services.*']);

            foreach ($buildingServicesHrBoiler as $buildingService) {
                if ($buildingService instanceof BuildingService) {
                    // this means we have to add something on the heat-source toolquestion
                    $serviceValue = $buildingService->serviceValue;
                    if (!$serviceValue instanceof ServiceValue) {
                        // so the user had nothing saved, which only happens on old accounts.
                        continue;
                    } else {
                        if (!isset($hrBoilerMap[$serviceValue->value])) {
                            // so the user did not state he has a hr boiler, thus we can continue.
                            continue;
                        }
                        $mappedToolQuestionAnswer = $hrBoilerMap[$serviceValue->value];
                    }

                    $data['input_source_id'] = $buildingService->input_source_id;

                    $data['tool_question_custom_value_id'] = ToolQuestionCustomValue::findByShort($mappedToolQuestionAnswer)->id;
                    $data['answer'] = $mappedToolQuestionAnswer;

                    DB::table('tool_question_answers')->insert($data);
                }
            }

            $buildingServicesHeatPump = $building
                ->buildingServices()
                ->allInputSources()
                ->leftJoin('services as s', 'building_services.service_id', '=', 's.id')
                ->where('s.short', 'heat-pump')->get(['building_services.*']);

            foreach ($buildingServicesHeatPump as $buildingService) {
                if ($buildingService instanceof BuildingService) {
                    // this means we have to add something on the heat-source toolquestion
                    $serviceValue = $buildingService->serviceValue;
                    if (!$serviceValue instanceof ServiceValue) {
                        $mappedToolQuestionAnswer = 'none';
                        $data['input_source_id'] = $buildingService->input_source_id;

                        $data['tool_question_custom_value_id'] = ToolQuestionCustomValue::findByShort($mappedToolQuestionAnswer)->id;
                        $data['answer'] = $mappedToolQuestionAnswer;
                        DB::table('tool_question_answers')->insert($data);
                    } else if ($serviceValue->value == "Geen") {
                        // check what kinda hr boiler the user has, if he selected onbekend or niet aanwezig we have to set the none option
                        $buildingServiceHrBoiler = $building
                            ->buildingServices()
                            ->forInputSource($buildingService->inputSource)
                            ->leftJoin('services as s', 'building_services.service_id', '=', 's.id')
                            ->where('s.short', 'hr-boiler')->first(['building_services.*']);

                        // this means the user said "i dont have a hr-boiler nor a heat-pump, so we will select the "none" option
                        if (in_array($buildingServiceHrBoiler->serviceValue->value, ['Niet aanwezig', 'Onbekend'])) {
                            $mappedToolQuestionAnswer = 'none';
                            $data['input_source_id'] = $buildingService->input_source_id;

                            $data['tool_question_custom_value_id'] = ToolQuestionCustomValue::findByShort($mappedToolQuestionAnswer)->id;
                            $data['answer'] = $mappedToolQuestionAnswer;
                            DB::table('tool_question_answers')->insert($data);
                        } else {
                            // this means we just go to the next one, if the user has a hr boiler and selected that he has no heat pump we have nothing to insert.
                            continue;
                        }

                    } else {
                        // can contain multiple if there was a hybrid one
                        $mappedToolQuestionAnswers = $heatPumpMap[$buildingService->serviceValue->calculate_value];
                        // 4 is a hybrid heat pump, it could be the user also manually selected he has a hr boiler
                        // so we have to delete all other rows for the user
                        if ($buildingService->serviceValue->calculate_value == 4) {
                            DB::table('tool_question_answers')
                                ->where('building_id', $building->id)
                                ->where('input_source_id', $buildingService->input_source_id)
                                ->where('tool_question_id', $toolQuestion->id)
                                ->delete();
                        }
                        foreach ($mappedToolQuestionAnswers as $mappedToolQuestionAnswer) {
                            $data['input_source_id'] = $buildingService->input_source_id;

                            $data['tool_question_custom_value_id'] = ToolQuestionCustomValue::findByShort($mappedToolQuestionAnswer)->id;
                            $data['answer'] = $mappedToolQuestionAnswer;
                            DB::table('tool_question_answers')->insert($data);
                        }
                    }
                }
            }
        }
        // todo: the hr-boiler and heat-pump service should be deleted from the database about now.
        $bar->finish();
        $this->output->newLine();
    }

    // so this method will map the question "HR CV Ketel" to "wat gebruikt u voor verwarming en warm water"
    private function mapBuildingFeatureBuildingHeatingToBuildingHeatingApplicationToolQuestion()
    {
        $buildingFeatures = BuildingFeature::allInputSources()
            ->whereHas('building')
            ->with(['building'])
            ->get();

        $bar = $this->output->createProgressBar($buildingFeatures->count());
        $bar->start();


        $buildingHeatingApplicationMap = [
            'radiators' => ['radiators'],
            'radiators-with-floor-heating' => ['radiators', 'floor-heating'],
            'low-temperature-heater' => ['low-temperature-heater'],
            'floor-wall-heating' => ['floor-heating'],
        ];
        $toolQuestion = ToolQuestion::findByShort('building-heating-application');
        foreach ($buildingFeatures as $buildingFeature) {
            // we could use whereNotNull, but that would mess up the test case, that can be done when going live.
            if (!is_null($buildingFeature->building_heating_application_id)) {
                $data = [
                    'tool_question_id' => $toolQuestion->id,
                    'input_source_id' => $buildingFeature->input_source_id,
                    'building_id' => $buildingFeature->building_id,
                ];

                $buildingHeatingApplicationShort = DB::table('building_heating_applications')
                    ->find($buildingFeature->building_heating_application_id)->short;
                // now map the old to the new answers, and create the tool question answers
                $buildingHeatingValueShorts = $buildingHeatingApplicationMap[$buildingHeatingApplicationShort];

                // and save each new map
                foreach ($buildingHeatingValueShorts as $toolQuestionCustomValueShort) {
                    $toolQuestionCustomValue = ToolQuestionCustomValue::findByShort($toolQuestionCustomValueShort);
                    $data['answer'] = $toolQuestionCustomValue->id;
                    $data['tool_question_custom_value_id'] = $toolQuestionCustomValue->id;
                    DB::table('tool_question_answers')->insert($data);
                }

            }
            $bar->advance();
        }
        $bar->finish();
        $this->output->newLine();
    }

    private function mapUserMotivations()
    {
        $users = User::has('building')
            ->with(['building.user'])
            ->get();

        // let me explain;
        // in the beginning we saved the order starting from 1, later on we saved the order starting from 0
        // so that's why there are multiple maps
        $orderToRatingMapWith0 = [
            0 => 5,
            1 => 4,
            2 => 3,
            3 => 3
        ];
        $orderToRatingMapWith1 = [
            1 => 5,
            2 => 4,
            3 => 3,
            4 => 3
        ];

        $motivationToRatingNameMap = [
            1 => 'comfort',
            2 => 'renewable',
            3 => 'lower-monthly-costs',
            4 => 'investment',
        ];
        $motivations = DB::table('motivations')->get();
        foreach ($users as $user) {
            // these do not exist in the user motivations.
            $answer = [
                'to-own-taste' => 3,
                'indoor-climate' => 3,
            ];
            $data = [
                'tool_question_id' => ToolQuestion::findByShort('comfort-priority')->id,
                'building_id' => $user->building->id,
                // the user motivations has no input_source_id, so we can do it this way.
                'input_source_id' => InputSource::findByShort('resident')->id,
            ];
            // as default
            $orderToRatingMap = $orderToRatingMapWith1;

            $userMotivations = DB::table('user_motivations')->where('user_id', $user->id)->get();
            if ($userMotivations->contains('order', 0)) {
                $orderToRatingMap = $orderToRatingMapWith0;
            }
            foreach ($motivations as $motivation) {
                $userMotivation = $userMotivations->where('motivation_id', $motivation->id)->first();

                // default the rating value to one, unless we can map it.
                $rating = 1;
                if ($userMotivation instanceof \stdClass) {
                    $rating = $orderToRatingMap[$userMotivation->order];
                }
                $answer[$motivationToRatingNameMap[$motivation->id]] = $rating;
            }

            $data['answer'] = json_encode($answer);
            DB::table('tool_question_answers')->insert($data);
        }
    }

    private function mapUserEnergyHabits()
    {

        $userEnergyHabits = UserEnergyHabit::allInputSources()
            ->whereHas('user.building')
            ->with('user.building')
            ->get();

        $bar = $this->output->createProgressBar($userEnergyHabits->count());
        $bar->start();

        foreach ($userEnergyHabits as $userEnergyHabit) {

            $toolQuestion = ToolQuestion::findByShort('cook-type');

            $cookGas = $userEnergyHabit->cook_gas;

            $data = [
                'tool_question_id' => $toolQuestion->id,
                'input_source_id' => $userEnergyHabit->input_source_id,
                'building_id' => $userEnergyHabit->user->building->id
            ];

            // now map the actual answer.
            if ($cookGas == 1 || $cookGas == 0) {
                $answer = 'gas';
            } else {
                $answer = 'electric';
            }

            $data['tool_question_custom_value_id'] = ToolQuestionCustomValue::findByShort($answer)->id;
            $data['answer'] = $answer;

            DB::table('tool_question_answers')->insert($data);

            $bar->advance();
        }
        $bar->finish();
        $this->output->newLine();
    }
}
