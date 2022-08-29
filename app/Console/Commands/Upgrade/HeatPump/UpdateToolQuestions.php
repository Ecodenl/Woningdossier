<?php

namespace App\Console\Commands\Upgrade\HeatPump;

use App\Models\Service;
use App\Models\ServiceValue;
use App\Models\Step;
use App\Models\SubStep;
use App\Models\ToolQuestion;
use App\Models\ToolQuestionCustomValue;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
        $this->infoLog('Starting ' . __CLASS__);
        // Ensure we clear the cache so we don't run into potentially `null` cached shorts.
        Artisan::call('cache:clear');

        $this->infoLog('Seeding scans, tool labels and steps');
        Artisan::call('db:seed', ['--class' => \ScansTableSeeder::class, '--force' => true]);
        Artisan::call('db:seed', ['--class' => \ToolLabelsTableSeeder::class, '--force' => true]);
        Artisan::call('db:seed', ['--class' => \StepsTableSeeder::class, '--force' => true]);

        // Before we can update the ToolQuestions, we must update the values
        $this->handlePreQuestionMap();

        $this->infoLog('Seeding tool questions');
        Artisan::call('db:seed', ['--class' => \ToolQuestionsTableSeeder::class, '--force' => true]);

        // Now the new questions are seeded, we need to map these as well
        $this->handlePostQuestionMap();

        $this->infoLog('Deleting all sub steppables');
        DB::table('sub_steppables')->truncate();

        $this->infoLog('Seeding sub steppables');
        Artisan::call('db:seed', ['--class' => \SubSteppablesTableSeeder::class, '--force' => true]);
    }

    private function handlePreQuestionMap()
    {
        $this->infoLog('Starting pre-question map');

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

            $total = $buildingsWithNoHeatPump->count();
            $this->infoLog("Starting 'none' service value map for a total of {$total} building_services");

            $heatPumpCustomValue = ToolQuestionCustomValue::where('tool_question_id', $heatSourceQuestion->id)
                ->whereShort('heat-pump')
                ->first();

            $step = Step::findByShort('residential-status');
            $subStep = SubStep::bySlug('verwarming')->first();

            $i = 0;
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

                // Now we also need to reset the sub step and step so the user is forced to recheck it (since they did
                // select a heat pump in the past)
                DB::table('completed_sub_steps')
                    ->where('sub_step_id', $subStep->id)
                    ->where('building_id', $buildingService->building_id)
                    ->where('input_source_id', $buildingService->input_source_id)
                    ->delete();

                DB::table('completed_steps')
                    ->where('step_id', $step->id)
                    ->where('building_id', $buildingService->building_id)
                    ->where('input_source_id', $buildingService->input_source_id)
                    ->delete();

                ++$i;

                if ($i % 100 === 0) {
                    $this->infoLog("{$i} / {$total}");
                }
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

        $this->infoLog('Starting heat pump service value map');

        $order = 1;
        foreach ($heatPumpValueMap as $newName => $info) {
            if (isset($info['old'])) {
                $oldServiceValue = ServiceValue::where('service_id', $heatPump->id)->byValue($info['old'])->first();
                if ($oldServiceValue instanceof ServiceValue) {
                    DB::table('service_values')
                        ->where('id', $oldServiceValue->id)
                        ->update([
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
                        ]),
                    ]);
                }
            }

            ++$order;
        }
    }

    private function handlePostQuestionMap()
    {
        $this->infoLog('Starting post-question map');

        $heatPump = Service::findByShort('heat-pump');
        $otherValue = ServiceValue::where('service_id', $heatPump->id)->byValue('Anders')->first();

        $buildingServices = DB::table('building_services')->where('service_id', $heatPump->id)
            ->where('service_value_id', $otherValue->id)
            ->get();

        $total = $buildingServices->count();
        $this->infoLog("Starting 'other' to 'collectieve warmtepomp' service value map for a total of {$total} building_services");

        $newQuestion = ToolQuestion::findByShort('heat-pump-other');

        $i = 0;
        // Map all "other" answers to "Collectieve warmtepomp" (as that was their old answer)
        foreach ($buildingServices as $buildingService) {
            DB::table('tool_question_answers')
                ->updateOrInsert(
                    [
                        'building_id' => $buildingService->building_id,
                        'input_source_id' => $buildingService->input_source_id,
                        'tool_question_id' => $newQuestion->id,
                    ],
                    [
                        'answer' => "Collectieve warmtepomp",
                    ]
                );

            ++$i;

            if ($i % 100 === 0) {
                $this->infoLog("{$i} / {$total}");
            }
        }

        $heatSourceQuestion = ToolQuestion::findByShort('heat-source');
        $heatSourceQuestionTapWater = ToolQuestion::findByShort('heat-source-warm-tap-water');

        $answers = DB::table('tool_question_answers')
            ->where('tool_question_id', $heatSourceQuestion->id)
            ->get();

        $total = $answers->count();
        $this->infoLog("Starting heat-source to heat-source-warm-tap-water map for a total of {$total} answers");

        $i = 0;
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
            ++$i;

            if ($i % 100 === 0) {
                $this->infoLog("{$i} / {$total}");
            }
        }

        // Map interest into now 2 separate questions
        $heatPumpInterest = ToolQuestion::findByShort('interested-in-heat-pump');
        $heatPumpInterestVariant = ToolQuestion::findByShort('interested-in-heat-pump-variant');

        $oldToNew = [
            'yes-hybrid-heat-pump' => 'hybrid-heat-pump',
            'yes-full-heat-pump' => 'full-heat-pump',
            'unsure' => 'unsure',
        ];

        $this->infoLog("Starting interested-in-heat-pump to interested-in-heat-pump-variant map");

        foreach ($oldToNew as $old => $new) {
            $oldValue = ToolQuestionCustomValue::where('tool_question_id', $heatPumpInterest->id)
                ->whereShort($old)
                ->first();

            // Keep atomic!
            if ($oldValue instanceof ToolQuestionCustomValue) {
                $newValue = ToolQuestionCustomValue::where('tool_question_id', $heatPumpInterestVariant->id)
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

        $this->infoLog("Starting considerable to 'new situation' considerable question map");

        // Map considerables to new question
        $considerableQuestion = ToolQuestion::findByShort('heat-source-considerable');
        $hrBoilerCustomValue = ToolQuestionCustomValue::where('tool_question_id', $considerableQuestion->id)
            ->whereShort('hr-boiler')
            ->first();
        $sunBoilerCustomValue = ToolQuestionCustomValue::where('tool_question_id', $considerableQuestion->id)
            ->whereShort('sun-boiler')
            ->first();
        $steps = [
            Step::findByShort('high-efficiency-boiler'),
            Step::findByShort('heater'),
        ];

        foreach ($steps as $considerableStep) {
            $customValueForStep = $considerableStep->short === 'heater' ? $sunBoilerCustomValue : $hrBoilerCustomValue;

            $usersThatConsiderStep = DB::table('considerables')
                ->where('considerable_type', Step::class)
                ->where('considerable_id', $considerableStep->id)
                ->where('is_considering', 1)
                ->get();

            $total = $usersThatConsiderStep->count();
            $this->infoLog("{$total} consider {$considerableStep->short}");

            $i = 0;
            foreach ($usersThatConsiderStep as $user) {
                $building = DB::table('buildings')->where('user_id', $user->user_id)->first();

                DB::table('tool_question_answers')
                    ->updateOrInsert([
                        'building_id' => $building->id,
                        'input_source_id' => $user->input_source_id,
                        'tool_question_id' => $considerableQuestion->id,
                        'tool_question_custom_value_id' => $customValueForStep->id,
                    ], ['answer' => $customValueForStep->short]);

                ++$i;

                if ($i % 100 === 0) {
                    $this->infoLog("{$i} / {$total}");
                }
            }

            DB::table('considerables')
                ->where('considerable_type', Step::class)
                ->where('considerable_id', $considerableStep->id)
                ->delete();
        }

        $this->infoLog('Deleting language lines');

        // Language lines to delete
        $languageLines = [
            'heater' => [
                'pv-panel-orientation-id.title',
                'pv-panel-orientation-id.help',
                'angle.title',
                'angle.help',
            ],
        ];

        foreach ($languageLines as $group => $keys) {
            DB::table('language_lines')
                ->where('group', $group)
                ->whereIn('key', $keys)
                ->delete();
        }
    }

    private function infoLog($info)
    {
        $this->info($info);
        Log::debug($info);
    }
}