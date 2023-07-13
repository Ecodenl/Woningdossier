<?php

namespace App\Console\Commands\Upgrade;

use App\Helpers\KengetallenCodes;
use App\Models\Step;
use App\Models\ToolQuestion;
use App\Services\Kengetallen\Resolvers\RvoDefined;
use Illuminate\Console\Command;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;

class CompleteGasElectricityCostOnRelevantBuildings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'buildings:complete-gas-electricity-sub-step';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Completes the gas-electricity substep for eligible buildings and its relevant input sources.';

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
        $usageQuickScan = Step::where('short', 'usage-quick-scan')->first();
        $subStepToComplete = $usageQuickScan->subSteps()->where('slug->nl', 'gas-en-elektra-kosten')->first();

        $electricityPriceEuroTq = ToolQuestion::findByShort('electricity-price-euro');
        $electricityPrice = (new RvoDefined())->get(KengetallenCodes::EURO_SAVINGS_ELECTRICITY);

        $gasPriceEuroTq = ToolQuestion::findByShort('gas-price-euro');
        $gasPrice = (new RvoDefined())->get(KengetallenCodes::EURO_SAVINGS_GAS);


        // old query which just takes a look at the completed main step, without taking the sub step into consideration
        DB::table('steps')
            ->selectRaw('steps.id as step_id, buildings.id as building_id, completed_steps.input_source_id')
            ->leftJoin('completed_steps', 'steps.id', 'completed_steps.step_id')
            ->join('buildings', 'completed_steps.building_id', 'buildings.id')
            ->where('steps.id', $usageQuickScan->id)
            ->orderBy('building_id')
            ->chunk(400, function ($buildingsWhoHaveCompletedUsageQuickScan) use (
                $usageQuickScan,
                $subStepToComplete,
                $electricityPriceEuroTq,
                $electricityPrice,
                $gasPriceEuroTq,
                $gasPrice
            ) {
                foreach ($buildingsWhoHaveCompletedUsageQuickScan as $buildingWhoHasCompletedUsageQuickScan) {
                    $incompleteGasElectricityForBuildingInputSourcesCombinations = DB::table('steps')
                        ->selectRaw('steps.id as step_id,
                                            completed_steps.building_id as building_id, 
                                            completed_steps.input_source_id'
                        )
                        ->leftJoin('completed_steps', 'steps.id', 'completed_steps.step_id')
                        ->leftJoin('completed_sub_steps', function (JoinClause $leftJoin) use ($subStepToComplete) {
                            $leftJoin->on('completed_steps.building_id', 'completed_sub_steps.building_id')
                                ->on('completed_steps.input_source_id', '=', 'completed_sub_steps.input_source_id')
                                ->where('completed_sub_steps.sub_step_id', '=', $subStepToComplete->id);
                        })
                        ->where('steps.id', $usageQuickScan->id)
                        // due to the left join we will also get rows which are not present in the completed_sub_steps, those with a empty sub step id!
                        ->whereNull('sub_step_id')
                        ->where('completed_steps.building_id', $buildingWhoHasCompletedUsageQuickScan->building_id)
                        ->orderBy('completed_steps.building_id')
                        ->get();
                    foreach ($incompleteGasElectricityForBuildingInputSourcesCombinations as $building) {
                        $this->info("Inserting for building {$building->building_id} and source {$building->input_source_id}");
                        // give the user the "missing" answers

                        DB::table('tool_question_answers')->updateOrInsert([
                            'tool_question_id' => $electricityPriceEuroTq->id,
                            'building_id' => $building->building_id,
                            'input_source_id' => $building->input_source_id,
                        ], ['answer' => $electricityPrice]);

                        DB::table('tool_question_answers')->updateOrInsert([
                            'tool_question_id' => $gasPriceEuroTq->id,
                            'building_id' => $building->building_id,
                            'input_source_id' => $building->input_source_id,
                        ], ['answer' => $gasPrice]);

                        $alreadyCompleted = DB::table('completed_sub_steps')
                            ->where('building_id', $building->building_id)
                            ->where('input_source_id', $building->input_source_id)
                            ->where('sub_step_id', $subStepToComplete->id)
                            ->exists();

                        if ($alreadyCompleted === false) {
                            DB::table('completed_sub_steps')
                                ->insert([
                                    'building_id' => $building->building_id,
                                    'input_source_id' => $building->input_source_id,
                                    'sub_step_id' => $subStepToComplete->id,
                                ]);
                        }
                    }
                }
            });
    }
}
