<?php

namespace App\Console\Commands\Upgrade\HeatPump;

use App\Helpers\DataTypes\Caster;
use App\Models\ExampleBuilding;
use App\Models\Service;
use App\Models\ServiceValue;
use App\Models\Step;
use App\Models\ToolQuestion;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class ExampleBuildingContentRestructure extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'upgrade:heat-pump:restructures-example-building-content';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Restructures the example building ';

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

        $exampleBuildings = ExampleBuilding::with('contents')->get();
        $bar = $this->output->createProgressBar(count($exampleBuildings));
        $bar->start();
        $stepSlugs = [
            'general-data',
            'ventilation',
            'wall-insulation',
            'floor-insulation',
            'hr-boiler',
            'heater',
            'solar-panels',
            'insulated-glazing',
            'roof-insulation',
            'high-efficiency-boiler'
        ];

        foreach ($exampleBuildings as $exampleBuilding) {
            foreach ($exampleBuilding->contents as $content) {
                // reset the content each time
                $newContent = [];
                foreach ($content->content as $stepSlug => $dataForStep) {
                    if (in_array($stepSlug, $stepSlugs)) {
                        foreach ($dataForStep as $subStepSlug => $dataForSubStep) {
                            foreach (Arr::dot($dataForSubStep) as $saveIn => $value) {

                                if ($stepSlug == 'ventilation') {
                                    // the save in is bogged, we have to correct it first.
                                    $saveIn = explode('.', $saveIn);
                                    array_pop($saveIn);
                                    $saveIn = implode('.', $saveIn);
                                }

                                if (stripos($saveIn, 'considerables') !== false && stripos($saveIn, 'is_considering') === false) {
                                    // fix considerable
                                    $saveIn .= '.is_considering';
                                }
                                if (Str::startsWith($saveIn, 'element')) {
                                    if (stripos($saveIn, 'extra') === false && stripos($saveIn, 'element_value_id') === false) {
                                        $saveIn .= '.element_value_id';
                                    }
                                    $saveIn = Str::replaceFirst('element', 'building_elements', $saveIn);
                                }
                                if (Str::startsWith($saveIn, 'service')) {
                                    if (stripos($saveIn, 'extra') === false && stripos($saveIn, 'service_value_id') === false) {
                                        $saveIn .= '.service_value_id';
                                    }
                                    $saveIn = Str::replaceFirst('service', 'building_services', $saveIn);
                                }

                                $toolQuestion = ToolQuestion::where('save_in', $saveIn)->first();



                                // the tool question answers are actually already in a ok format
                                // however they need some small adjustments
                                if (Str::contains($saveIn, 'tool_question_answers')) {
                                    $saveIn = explode('.', $saveIn);
                                    // kick of the prefix so we just have the short with its index
                                    array_shift($saveIn);
                                    // the save in here is actually already a tool question short!
                                    $saveIn = implode('.', $saveIn);

                                    $toolQuestion = ToolQuestion::findByShort($saveIn);
                                    if ($toolQuestion instanceof ToolQuestion) {
                                        // still a custom map becuase the old way saved it wrong..
                                        if ($toolQuestion->data_type == Caster::ARRAY) {
                                            $newContent[$saveIn][] = $value;
                                        } else {
                                            data_set($newContent, $saveIn, $value);
                                        }
                                    }

                                } else if ($saveIn === 'building_services.3.service_value_id' && !is_null($value)) {
                                    // previously the answer for the sun-boiler was saved in the sun boiler service itself
                                    $sunBoilerService = Service::findByShort('sun-boiler');
                                    // we will map them to the heat source and heat source warm water, since its split up.
                                    $heatSourceQuestion = ToolQuestion::findByShort('heat-source');
                                    $heatSourceWaterQuestion = ToolQuestion::findByShort('heat-source-warm-tap-water');


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
                                        $waterValue->id => [
                                            $heatSourceWaterQuestion->short => $heatSourceWaterAnswer
                                        ],
                                        $heatingValue->id => [
                                            $heatSourceQuestion->short => $heatSourceAnswer
                                        ],
                                        $bothValue->id => [
                                            $heatSourceQuestion->short => $heatSourceAnswer,
                                            $heatSourceWaterQuestion->short => $heatSourceWaterAnswer
                                        ],
                                    ];

                                    if ($value != $noneValue->id) {
                                        foreach ($mapping[$value] as $toolQuestionShort => $toolQuestionCustomValue) {
                                            $newContent[$toolQuestionShort][] = $toolQuestionCustomValue->short;
                                        }
                                    }
                                } else if ($toolQuestion instanceof ToolQuestion) {


                                    $shouldSet = true;
                                    if ($value === null) {
                                        $shouldSet = false;
                                    }
                                    if ($value == "null") {
                                        $shouldSet = false;
                                    }
                                    if ($shouldSet) {
                                        if (in_array($toolQuestion->short, ['ventilation-how', 'ventilation-living-situation', 'ventilation-usage'])) {
                                            $newContent[$toolQuestion->short][] = $value;
                                        } else {
                                            $newContent[$toolQuestion->short] = $value;
                                        }
                                    }
                                }
                            }
                        }
                        $content->update(['content' => $newContent]);
                    } else {
                        $this->info("Step $stepSlug not found; breaking (is this correct, should only be shown on second run.)");
                        break;
                    }
                }
                // update the content of the example building content

            }
            $bar->advance();
        }
        $bar->finish();
    }
}