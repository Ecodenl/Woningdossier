<?php

namespace App\Console\Commands\Upgrade;

use App\Models\BuildingFeature;
use App\Models\InputSource;
use App\Models\Motivation;
use App\Models\ToolQuestion;
use App\Models\ToolQuestionCustomValue;
use App\Models\User;
use App\Models\UserEnergyHabit;
use App\Models\UserMotivation;
use App\ToolQuestionAnswer;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
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


        $this->info("Mapping user energy habits...");
//        $this->info('Cook gas field to the tool question answers...');
//        $this->mapUserEnergyHabits();
//        $this->info("Mapping the user motivations to the welke zaken vind u belangrijke rating slider style...");
//        $this->mapUserMotivations();
//        $this->info('Mapping building heating applications from building features to tool question building heating application');
//        $this->mapBuildingFeatureBuildingHeatingToBuildingHeatingApplicationToolQuestion();

    }

    // so this method will map the question "HR CV Ketel" to "wat gebruikt u voor verwarming en warm water"
    private function mapBuildingFeatureBuildingHeatingToBuildingHeatingApplicationToolQuestion()
    {
        $buildingFeatures = BuildingFeature::allInputSources()
            ->whereHas('building')
            ->with(['building', 'buildingHeatingApplication'])
            ->limit(500)
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
            if(!is_null($buildingFeature->building_heating_application_id)) {
                $data = [
                    'tool_question_id' => $toolQuestion->id,
                    'input_source_id' => $buildingFeature->input_source_id,
                    'building_id' => $buildingFeature->building_id,
                ];

                $buildingHeatingApplicationShort = $buildingFeature->buildingHeatingApplication->short;

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
    }

    private function mapUserMotivations()
    {
        $users = User::has('building')
            ->has('motivations')
            ->with(['building.user', 'motivations.motivation'])
            ->limit(500)
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
        $motivations = Motivation::all();
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

            if ($user->motivations->contains('order', 0)) {
                $orderToRatingMap = $orderToRatingMapWith0;
            }
            foreach ($motivations as $motivation) {
                $userMotivation = $user->motivations->where('motivation_id', $motivation->id)->first();

                // default the rating value to one, unless we can map it.
                $rating = 1;
                if ($userMotivation instanceof UserMotivation) {
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
            ->limit(500)
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
            if ($cookGas == 1) {
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
    }
}
