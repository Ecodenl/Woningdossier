<?php

namespace App\Console\Commands\Upgrade;

use App\Models\Cooperation;
use App\Models\ExampleBuilding;
use App\Models\ToolQuestion;
use Illuminate\Console\Command;

class GiveSpecificExampleBuildingToolQuestionValues extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'upgrade:map-example-buildings-to-tool-question';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will give the specific-example-building the correct values (example buildings)';

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
        $exampleBuildingsForCooperation = ExampleBuilding::withoutGlobalScopes()
            ->get()->groupBy('cooperation_id');

        $toolQuestion = ToolQuestion::findByShort('specific-example-building');
        foreach ($exampleBuildingsForCooperation as $cooperationId => $exampleBuildings) {
            foreach ($exampleBuildings as $order => $exampleBuilding) {

                $insertData = [
                    'tool_question_id' => $toolQuestion->id,
                    'order' => $order,
                    'show' => true,
                    'tool_question_valuable_type' => ExampleBuilding::class,
                    'tool_question_valuable_id' => $exampleBuilding->id,
                ];

                if (!is_null($exampleBuilding->cooperation_id)) {
                    $insertData['limiteable_id'] = $exampleBuilding->cooperation_id;
                    $insertData['limiteable_type'] = Cooperation::class;
                }

                \DB::table('tool_question_valuables')->updateOrInsert([
                    'order' => $order,
                    'tool_question_id' => $toolQuestion->id,
                    'tool_question_valuable_id' => $exampleBuilding->id,
                ], $insertData);
            }
        }
    }
}
