<?php

namespace App\Console\Commands\Upgrade\HeatPump;

use App\Models\Service;
use App\Models\ServiceValue;
use App\Models\ToolQuestion;
use App\Models\ToolQuestionCustomValue;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class UpdateToolQuestions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'upgrade:heat-pump:update-tool-questions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the tool questions in the format for the heat pump, this includes "hardcoded" updates on translations.';

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
        // Ensure we clear the cache so we don't run into potentially `null` cached shorts.
        Artisan::call('cache:clear');

        Artisan::call('db:seed', ['--class' => \ScansTableSeeder::class]);
        Artisan::call('db:seed', ['--class' => \StepsTableSeeder::class]);

        // Before we can update the ToolQuestions, we must update the values
        //$this->handlePreQuestionMap(); // TODO test before enabling

        Artisan::call('db:seed', ['--class' => \ToolQuestionsTableSeeder::class]);

        // Now the new questions are seeded, we need to map these as well
        //$this->handlePostQuestionMap(); // TODO test before enabling

        Artisan::call('db:seed', ['--class' => \SubStepsTableSeeder::class]);



    }

    private function handlePreQuestionMap()
    {
        // Update question names
        $heatSourceQuestion = ToolQuestion::findByShort('heat-source');
        $heatSourceQuestion->update(['name' => ['nl' => 'Wat wordt er gebruikt voor verwarming']]);

        ToolQuestion::findByShort('heat-pump-type')->update(['name' => ['nl' => 'Wat voor type warmtepomp is er?']]);
        ToolQuestion::findByShort('interested-in-heat-pump')->update(['name' => ['nl' => 'Overweeg je om een warmtepomp te nemen?']]);

        $heatPump = Service::findByShort('heat-pump');
        $none = ServiceValue::where('service_id', $heatPump->id)->byValue('Geen')->first();

        // Keep atomic!
        if ($none instanceof ServiceValue) {
            $buildingsWithNoHeatPump = DB::table('building_services')
                ->where('service_id', $heatPump->id)
                ->where('service_value_id', $none->id)
                ->get();
            $heatPumpCustomValue = ToolQuestionCustomValue::where('tool_question_id', $heatSourceQuestion->id)
                ->whereShort('heat-pump')
                ->first();

            // Set "none" to `null` and "uncheck" heat-pump by the heat source question
            foreach ($buildingsWithNoHeatPump as $buildingService) {
                DB::table('tool_question_answers')->where('tool_question_id', $heatSourceQuestion->id)
                    ->where('tool_question_custom_value_id', $heatPumpCustomValue->id)
                    ->where('building_id', $buildingService->building_id)
                    ->where('input_source_id', $buildingService->input_source_id)
                    ->delete();

                DB::table('building_services')
                    ->where('building_id', $buildingService->building_id)
                    ->where('input_source_id', $buildingService->input_source_id)
                    ->where('service_id', $heatPump->id)
                    ->where('service_value_id', $none->id)
                    ->update([
                        'service_value_id' => null,
                    ]);
            }

            $none->delete();
        }

        // Change service values for heat pump
        $heatPumpValueMap = [
            'Hybride warmtepomp met buitenlucht' => [
                'old' => 'Hybride warmtepomp',
                'comfort' => 2,
            ],
            'Hybride warmtepomp met ventilatielucht' => [
                'comfort' => 2,
            ],
            'Hybride warmtepomp met pvt panelen' => [
                'comfort' => 2,
            ],
            'Volledige warmtepomp met bodemwarmte' => [
                'old' => 'Volledige warmtepomp bodem',
                'comfort' => 3,
            ],
            'Volledige warmtepomp met buitenlucht' => [
                'old' => 'Volledige warmtepomp buitenlucht',
                'comfort' => 3,
            ],
            'Volledige warmtepomp met pvt panelen' => [
                'comfort' => 3,
            ],
            'Anders' => [
                'old' => 'Collectieve warmtepomp',
                'comfort' => 0,
            ],
        ];

        $order = 1;
        foreach ($heatPumpValueMap as $newName => $info) {
            if (isset($info['old'])) {
                $oldServiceValue = ServiceValue::where('service_id', $heatPump->id)->byValue($info['old'])->first();
                if ($oldServiceValue instanceof ServiceValue) {
                    $oldServiceValue->update([
                        'value' => json_encode(['nl' => $newName]),
                        'calculate_value' => $order,
                        'order' => $order,
                        'configurations' => json_encode([
                            'comfort' => $info['comfort'],
                        ]),
                    ]);
                }
            } else {
                $newValue = ServiceValue::where('service_id', $heatPump->id)->byValue($newName)->first();

                if (! $newValue instanceof ServiceValue) {
                    DB::table('service_values')->insert([
                        'service_id' => $heatPump->id,
                        'value' => json_encode(['nl' => $newName]),
                        'calculate_value' => $order,
                        'order' => $order,
                        'configurations' => json_encode([
                            'comfort' => $info['comfort'],
                        ])
                    ]);
                }
            }

            ++$order;
        }
    }

    private function handlePostQuestionMap()
    {
        $heatSourceQuestion = ToolQuestion::findByShort('heat-source');
        $heatSourceQuestionTapWater = ToolQuestion::findByShort('heat-source-warm-tap-water');

        $answers = DB::table('tool_question_answers')
            ->where('tool_question_id', $heatSourceQuestion->id)
            ->get();

        // Map relevant answers from heat-source to heat-source-warm-tap-water
        foreach ($answers as $answer) {
            if ($answer->answer !== 'infrared') {
                $customValueId = ToolQuestionCustomValue::where('tool_question_id', $heatSourceQuestionTapWater->id)
                    ->whereShort($answer->answer)
                    ->first()->id;
                DB::table('tool_question_answers')
                    ->updateOrInsert([
                        'building_id' => $answer->building_id,
                        'input_source_id' => $answer->input_source_id,
                        'tool_question_id' => $heatSourceQuestionTapWater->id,
                        'tool_question_custom_value_id' => $customValueId,
                    ], ['answer' => $answer->answer]);
            }
        }

        // Map interest
        $heatPumpInterest = ToolQuestion::findByShort('interested-in-heat-pump');
        $heatPumpInterestVariant = ToolQuestion::findByShort('interested-in-heat-pump-variant');

        $oldToNew = [
            'yes-hybrid-heat-pump' => 'hybrid-heat-pump',
            'yes-full-heat-pump' => 'full-heat-pump',
            'unsure' => 'unsure',
        ];

        foreach ($oldToNew as $old => $new) {
            $oldValue = ToolQuestionCustomValue::where('tool_question_id', $heatPumpInterest->id)
                ->whereShort($old)
                ->first();

            // Keep atomic!
            if ($oldValue instanceof ToolQuestionCustomValue) {
                $newValue =  ToolQuestionCustomValue::where('tool_question_id', $heatPumpInterestVariant->id)
                    ->whereShort($new)
                    ->first();

                DB::table('tool_question_answers')
                    ->where('tool_question_id', $heatPumpInterest)
                    ->where('tool_question_custom_value_id', $oldValue)
                    ->update([
                        'tool_question_id' => $heatPumpInterestVariant->id,
                        'tool_question_custom_value_id' => $newValue->id,
                        'answer' => $newValue->short,
                    ]);

                $oldValue->delete();
            }
        }
    }
}
