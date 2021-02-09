<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckForDuplicates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:duplicates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for duplicates in various tables.';

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

        $this->table(
            ['building_services', 'building_elements', 'user_interests', 'user_action_plan_advices'],
            [
                [$this->buildingServicesDuplicateCount(), $this->buildingElementsExceptWoodElementsDuplicateCount(), $this->userInterestsDuplicateCount(), $this->userActionPlanAdvicesDuplicateCount()]
            ]
        );
    }

    private function userInterestsDuplicateCount()
    {
        return DB::table('user_interests')
            ->selectRaw('user_id, input_source_id, interested_in_type, interested_in_id, count(*)')
            ->groupBy([
                'user_id',
                'input_source_id',
                'interested_in_type',
                'interested_in_id',
            ])
            ->having('count(*)', '>', 1)
            ->get()->count();

    }

    private function userActionPlanAdvicesDuplicateCount()
    {
        return DB::table('user_action_plan_advices')
            ->selectRaw('user_id, input_source_id, measure_application_id, step_id, count(*)')
            ->groupBy([
                'user_id',
                'input_source_id',
                'measure_application_id',
                'step_id',
            ])
            ->having('count(*)', '>', 1)
            ->get()->count();

    }

    private function buildingServicesDuplicateCount()
    {
        return DB::table('building_services')
            ->selectRaw('building_id, input_source_id, service_id, count(*)')
            ->groupBy([
                'building_id',
                'input_source_id',
                'service_id',
            ])
            ->having('count(*)', '>', 1)
            ->get()->count();
    }

    /** Wood elements are a different cup of tea, and currently has 0 duplicates */
    private function buildingElementsExceptWoodElementsDuplicateCount()
    {
        // Get all the duplicate building elements, grouped on inputsource, elementid en building id
        return DB::table('building_elements')
            ->selectRaw('building_id, input_source_id, element_id, count(*)')
            ->where('element_id', '!=', 8)
            ->groupBy([
                'building_id',
                'input_source_id',
                'element_id',
            ])
            ->having('count(*)', '>', 1)
            ->get()->count();
    }
}
