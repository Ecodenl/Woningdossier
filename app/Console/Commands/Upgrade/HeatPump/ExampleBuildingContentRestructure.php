<?php

namespace App\Console\Commands\Upgrade\HeatPump;

use App\Models\ExampleBuilding;
use App\Models\ExampleBuildingContent;
use App\Models\ToolQuestion;
use App\Services\ConsiderableService;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
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
        $missingSaveIn = [];

        $exampleBuildings = ExampleBuilding::with('contents')->where('id', 91)->get();

        $newContent = [];

        $missingSaveIn = [];


        // todo: this has been refactored to the heat-source heat-source-warm-tapwater
        // which means we should map the eb the same as the tool questions
        // "building_services.3.service_value_id" => "building_services.3.service_value_id"

        foreach ($exampleBuildings as $exampleBuilding) {
            foreach ($exampleBuilding->contents as $content) {
                foreach ($content->content as $stepSlug => $dataForStep) {

                    foreach ($dataForStep as $subStepSlug => $dataForSubStep) {
                        foreach (Arr::dot($dataForSubStep) as $saveIn => $value) {
                            if ($stepSlug == 'ventilation') {
                                // the save in is bogged, we have to correct it first.
                                $saveIn = explode('.', $saveIn);
                                array_pop($saveIn);
                                $saveIn = implode('.', $saveIn);
                            }
//                            if ($stepSlug == 'insulated-glazing') {
//                                dd($saveIn, $dataForSubStep);
//                            }
                            if (stripos($saveIn, 'considerables') !== false) {
                                // fix considerable
                                $saveIn.='.is_considering';
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
                            $toolQuestion = ToolQuestion::where('save_in', $saveIn)
                                ->first();

                            if ($toolQuestion instanceof ToolQuestion) {
                                $newContent[$toolQuestion->short] = $value;
                            } else {
                                $missingSaveIn[$saveIn] = $saveIn;
                            }
                        }
                    }
                }
            }
        }
        dd($missingSaveIn);
    }
}