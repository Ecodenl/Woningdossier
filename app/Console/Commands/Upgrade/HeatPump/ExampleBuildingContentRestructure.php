<?php

namespace App\Console\Commands\Upgrade\HeatPump;

use App\Models\ExampleBuildingContent;
use App\Services\ConsiderableService;
use Illuminate\Console\Command;

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
        $exampleBuildingContent = ExampleBuildingContent::first();

        $contents = $exampleBuildingContent->content;

        foreach ($contents as $stepSlug => $dataForStep) {
            foreach ($dataForStep as $subStepSlug => $dataForSubStep) {
                foreach ($dataForSubStep as $columnOrTable => $values) {

                    if ('considerables' == $columnOrTable) {
                        foreach ($values as $modelClass => $modelConsideration) {
                            foreach ($modelConsideration as $id => $considering) {
                                dd($modelConsideration, $id, $considering);
                            }
                        }
                    }
                }
            }
        }
    }
}
