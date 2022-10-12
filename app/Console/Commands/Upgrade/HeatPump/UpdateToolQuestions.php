<?php

namespace App\Console\Commands\Upgrade\HeatPump;

use App\Helpers\Conditions\ConditionEvaluator;
use App\Models\Building;
use App\Models\CompletedStep;
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

        $this->infoLog('Seeding scans, tool labels, and tool question types');
        Artisan::call('db:seed', ['--class' => \ScansTableSeeder::class, '--force' => true]);
        Artisan::call('db:seed', ['--class' => \ToolLabelsTableSeeder::class, '--force' => true]);
        Artisan::call('db:seed', ['--class' => \ToolQuestionTypesTableSeeder::class, '--force' => true]);

        $this->infoLog('Deleting all tool calculation results');
        DB::table('tool_calculation_results')->truncate();

        $this->infoLog('Seeding tool calculation results and steps');
        Artisan::call('db:seed', ['--class' => \ToolCalculationResultsTableSeeder::class, '--force' => true]);
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

        $shorts = [
            'new-water-comfort', 'new-heat-pump-type', 'new-boiler-type',
        ];

        foreach ($shorts as $short) {
            $tq = ToolQuestion::findByShort($short);
            if ($tq instanceof ToolQuestion) {
                DB::table('tool_question_valuables')->where('tool_question_id', $tq->id)->delete();
            }
        }

        // Update question names
        $heatSourceQuestion = ToolQuestion::findByShort('heat-source');
        $heatSourceQuestion->update(['name' => ['nl' => 'Wat wordt er gebruikt voor verwarming']]);

        ToolQuestion::findByShort('heat-pump-type')->update(['name' => ['nl' => 'Wat voor type warmtepomp is er?']]);

        $heatPump = Service::findByShort('heat-pump');
        $none = ServiceValue::where('service_id', $heatPump->id)->byValue('Geen')->first();

        // Keep atomic!
        if ($none instanceof ServiceValue) {
            $buildingsWithNoHeatPumpQuery = DB::table('building_services')
                ->where('service_id', $heatPump->id)
                ->where('service_value_id', $none->id);

            $total = $buildingsWithNoHeatPumpQuery->count();
            $this->infoLog("Starting 'none' service value map for a total of {$total} building_services");

            $heatPumpCustomValue = ToolQuestionCustomValue::where('tool_question_id', $heatSourceQuestion->id)
                ->whereShort('heat-pump')
                ->first();

            $step = Step::findByShort('residential-status');
            $subStep = SubStep::bySlug('verwarming')->first();

            $i = 0;

            $buildingsWithNoHeatPumpQuery->orderBy('id')->chunkById(100, function ($buildingsWithNoHeatPump) use (&$i, $total, $heatPump, $step, $subStep, $heatSourceQuestion, $heatPumpCustomValue, $none) {
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

                    if ($i % 1000 === 0) {
                        $this->infoLog("{$i} / {$total}");
                    }
                }
            });

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
            'Volledige warmtepomp met buitenlucht' => [
                'old' => 'Volledige warmtepomp buitenlucht',
                'comfort' => 3,
            ],
            'Volledige warmtepomp met bodemwarmte' => [
                'old' => 'Volledige warmtepomp bodem',
                'comfort' => 3,
            ],
            'Volledige warmtepomp met pvt panelen' => [
                'comfort' => 3,
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

        $heatSourceQuestion = ToolQuestion::findByShort('heat-source');
        $heatSourceWaterQuestion = ToolQuestion::findByShort('heat-source-warm-tap-water');

        // To keep it faster
        $processedBuildings = DB::table('tool_question_answers')
            ->where('tool_question_id', $heatSourceWaterQuestion->id)
            ->distinct()
            ->pluck('building_id')
            ->toArray();

        $answersQuery = DB::table('tool_question_answers')
            ->where('tool_question_id', $heatSourceQuestion->id)
            ->whereNotIn('building_id', $processedBuildings);

        $total = $answersQuery->count();
        $this->infoLog("Starting heat-source to heat-source-warm-tap-water map for a total of {$total} answers");

        $i = 0;

        $answersQuery->orderBy('id')->chunkById(100, function ($answers) use (&$i, $total, $heatSourceQuestion, $heatSourceWaterQuestion) {
            // Map relevant answers from heat-source to heat-source-warm-tap-water
            foreach ($answers as $answer) {
                if ($answer->answer !== 'infrared') {
                    $customValueId = ToolQuestionCustomValue::where('tool_question_id', $heatSourceWaterQuestion->id)
                        ->whereShort($answer->answer)
                        ->first()->id;
                    DB::table('tool_question_answers')
                        ->updateOrInsert([
                            'building_id' => $answer->building_id,
                            'input_source_id' => $answer->input_source_id,
                            'tool_question_id' => $heatSourceWaterQuestion->id,
                            'tool_question_custom_value_id' => $customValueId,
                        ], ['answer' => $answer->answer]);
                }
                ++$i;

                if ($i % 1000 === 0) {
                    $this->infoLog("{$i} / {$total}");
                }
            }
        });

        $heaterTypeQuestion = ToolQuestion::findByShort('heater-type');
        if ($heaterTypeQuestion instanceof ToolQuestion) {
            $sunBoilerService = Service::findByShort('sun-boiler');
            $noneValue = ServiceValue::where('service_id', $sunBoilerService->id)
                ->where('calculate_value', 1)->first();
            $waterValue = ServiceValue::where('service_id', $sunBoilerService->id)
                ->where('calculate_value', 2)->first();
            $heatingValue = ServiceValue::where('service_id', $sunBoilerService->id)
                ->where('calculate_value', 3)->first();
            $bothValue = ServiceValue::where('service_id', $sunBoilerService->id)
                ->where('calculate_value', 4)->first();

            $heatSourceAnswer = $heatSourceQuestion->toolQuestionCustomValues()->whereShort('sun-boiler')->first();
            $heatSourceWaterAnswer = $heatSourceWaterQuestion->toolQuestionCustomValues()->whereShort('sun-boiler')->first();

            $mapping = [
                $waterValue->id => [$heatSourceWaterAnswer],
                $heatingValue->id => [$heatSourceAnswer],
                $bothValue->id => [$heatSourceAnswer, $heatSourceWaterAnswer],
            ];

            $boilerQuery = DB::table('building_services')
                ->where('service_id', $sunBoilerService->id)
                ->where('service_value_id', '!=', $noneValue->id)
                ->whereNotNull('service_value_id');

            $total = $boilerQuery->count();

            $this->infoLog("Starting heater-type to heat-source/-warm-tap-water map for a total of {$total} building_services");

            $i = 0;

            $boilerQuery->orderBy('id')->chunkById(100, function ($buildingServices) use (&$i, $total, $mapping) {
                foreach ($buildingServices as $buildingService) {
                    foreach ($mapping[$buildingService->service_value_id] as $customValue) {
                        DB::table('tool_question_answers')->updateOrInsert(
                            [
                                'building_id' => $buildingService->building_id,
                                'input_source_id' => $buildingService->input_source_id,
                                'tool_question_id' => $customValue->tool_question_id,
                                'tool_question_custom_value_id' => $customValue->id,
                            ],
                            [
                                'answer' => $customValue->short,
                            ]
                        );
                    }

                    ++$i;

                    if ($i % 1000 === 0) {
                        $this->infoLog("{$i} / {$total}");
                    }
                }
            });

            $heaterTypeQuestion->delete();
        }

        $heatPump = Service::findByShort('heat-pump');
        $collectiveValue = ServiceValue::where('service_id', $heatPump->id)->byValue('Collectieve warmtepomp')->first();

        // Keep atomic!
        if ($collectiveValue instanceof ServiceValue) {
            $buildingServicesQuery = DB::table('building_services')->where('service_id', $heatPump->id)
                ->where('service_value_id', $collectiveValue->id);

            $total = $buildingServicesQuery->count();
            $this->infoLog("Starting 'Collectieve warmtepomp' to 'other' map for a total of {$total} building_services");

            $heatSourceOtherQuestion = ToolQuestion::findByShort('heat-source-other');
            $heatSourceWaterOtherQuestion = ToolQuestion::findByShort('heat-source-warm-tap-water-other');
            $heatSourceOtherCustomValue = ToolQuestionCustomValue::where('tool_question_id', $heatSourceQuestion->id)
                ->whereShort('none')
                ->first();
            $heatSourceWaterOtherCustomValue = ToolQuestionCustomValue::where('tool_question_id', $heatSourceWaterQuestion->id)
                ->whereShort('none')
                ->first();

            $i = 0;

            $buildingServicesQuery->orderBy('id')->chunkById(100, function ($buildingServices) use (&$i, $total, $heatSourceQuestion, $heatSourceOtherQuestion, $heatSourceWaterQuestion, $heatSourceWaterOtherQuestion, $heatSourceOtherCustomValue, $heatSourceWaterOtherCustomValue, $heatPump, $collectiveValue) {
                // Map all "Collectieve warmtepomp" to 'Other' (as that was their old answer)
                foreach ($buildingServices as $buildingService) {
                    DB::table('tool_question_answers')
                        ->updateOrInsert(
                            [
                                'building_id' => $buildingService->building_id,
                                'input_source_id' => $buildingService->input_source_id,
                                'tool_question_id' => $heatSourceQuestion->id,
                                'tool_question_custom_value_id' => $heatSourceOtherCustomValue->id,
                            ],
                            [
                                'answer' => $heatSourceOtherCustomValue->short,
                            ]
                        );

                    DB::table('tool_question_answers')
                        ->updateOrInsert(
                            [
                                'building_id' => $buildingService->building_id,
                                'input_source_id' => $buildingService->input_source_id,
                                'tool_question_id' => $heatSourceOtherQuestion->id,
                            ],
                            [
                                'answer' => "Collectieve warmtepomp",
                            ]
                        );

                    DB::table('tool_question_answers')
                        ->updateOrInsert(
                            [
                                'building_id' => $buildingService->building_id,
                                'input_source_id' => $buildingService->input_source_id,
                                'tool_question_id' => $heatSourceWaterQuestion->id,
                                'tool_question_custom_value_id' => $heatSourceWaterOtherCustomValue->id,
                            ],
                            [
                                'answer' => $heatSourceWaterOtherCustomValue->short,
                            ]
                        );

                    DB::table('tool_question_answers')
                        ->updateOrInsert(
                            [
                                'building_id' => $buildingService->building_id,
                                'input_source_id' => $buildingService->input_source_id,
                                'tool_question_id' => $heatSourceWaterOtherQuestion->id,
                            ],
                            [
                                'answer' => "Collectieve warmtepomp",
                            ]
                        );

                    DB::table('building_services')
                        ->where('building_id', $buildingService->building_id)
                        ->where('input_source_id', $buildingService->input_source_id)
                        ->where('service_id', $heatPump->id)
                        ->where('service_value_id', $collectiveValue->id)
                        ->update([
                            'service_value_id' => null,
                        ]);

                    ++$i;

                    if ($i % 1000 === 0) {
                        $this->infoLog("{$i} / {$total}");
                    }
                }
            });

            $collectiveValue->delete();
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

            $usersThatConsiderStepQuery = DB::table('considerables')
                ->where('considerable_type', Step::class)
                ->where('considerable_id', $considerableStep->id)
                ->where('is_considering', 1);

            $total = $usersThatConsiderStepQuery->count();
            $this->infoLog("{$total} consider {$considerableStep->short}");

            $i = 0;

            $usersThatConsiderStepQuery->orderBy('id')->chunkById(100, function ($usersThatConsiderStep) use (&$i, $total, $considerableQuestion, $customValueForStep) {
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

                    if ($i % 1000 === 0) {
                        $this->infoLog("{$i} / {$total}");
                    }
                }
            });

            DB::table('considerables')
                ->where('considerable_type', Step::class)
                ->where('considerable_id', $considerableStep->id)
                ->delete();
        }

        $residentialStatus = Step::findByShort('residential-status');
        $completedStepsQuery = CompletedStep::allInputSources()->where('step_id', $residentialStatus->id);

        $total = $completedStepsQuery->count();

        $this->infoLog("Checking if we should incomplete the 'woonstatus' step for {$total} completed steps");

        $i = 0;

        $completedStepsQuery->orderBy('id')->chunkById(100, function ($completedSteps) use (&$i, $total) {
            foreach ($completedSteps as $completedStep) {
                $building = $completedStep->building;
                if (! $building instanceof Building) {
                    $building = DB::table('buildings')->where('id', $completedStep->building_id)->first();

                    // If a building is deleted, we don't need to notify
                    if ($building instanceof \stdClass && empty($building->deleted_at)) {
                        $this->infoLog("Skipping completed_step with ID {$completedStep->id} for non-existent building ({$completedStep->building_id})");
                    }
                    continue;
                }

                $inputSource = $completedStep->inputSource;
                $step = $completedStep->step;

                $irrelevantSubSteps = $building->completedSubSteps()->forInputSource($inputSource)
                    ->pluck('sub_step_id')->toArray();

                $incompleteSubSteps = $step->subSteps()
                    ->whereNotIn('id', $irrelevantSubSteps)
                    ->orderBy('order')
                    ->get();

                $evaluator = ConditionEvaluator::init()
                    ->building($building)
                    ->inputSource($inputSource);

                $shouldIncomplete = false;

                foreach ($incompleteSubSteps as $incompleteSubStep) {
                    if ($evaluator->evaluate($incompleteSubStep->conditions ?? [])) {
                        $shouldIncomplete = true;
                        break;
                    }
                }

                if ($shouldIncomplete) {
                    $completedStep->delete();
                }

                ++$i;

                if ($i % 1000 === 0) {
                    $this->infoLog("{$i} / {$total}");
                }
            }
        });


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