<?php

namespace App\Console\Commands\Upgrade\HeatPump;

use App\Models\ExampleBuildingContent;
use App\Models\ToolQuestion;
use App\Services\ConsiderableService;
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
        $missingSaveIn = [];
        $exampleBuildingContent = ExampleBuildingContent::find(122);

        $contents = $exampleBuildingContent->content;
        $newContent = [];

        foreach ($contents as $stepSlug => $dataForStep) {
            foreach ($dataForStep as $subStepSlug => $dataForSubStep) {
                foreach (Arr::dot($dataForSubStep) as $saveIn => $value) {
                    if (Str::startsWith($saveIn, 'element')) {
                        $saveIn = Str::replaceFirst('element', 'building_elements', $saveIn);
                    }
                    if (Str::startsWith($saveIn, 'service')) {
                        $saveIn = Str::replaceFirst('service', 'building_services', $saveIn);
                    }
                    $toolQuestion = ToolQuestion::where('save_in', $saveIn)
                        ->first();

                    if ($toolQuestion instanceof ToolQuestion) {
                        $newContent[$toolQuestion->short] = $value;
                    } else {
                        $missingSaveIn[] = $saveIn;
                    }
                }
            }
        }
        asort($missingSaveIn);
    }
}
