<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixDuplicates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:duplicates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command which will locate duplicates, and deletes the oldest duplicates';

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
        $this->info("Correcting building elements....");
        $this->correctBuildingElementsExceptWoodElements();
        $this->info("Correcting building services...");
        $this->correctBuildingServices();
        $this->info("Correcting user interests..");
        $this->correctUserInterests();
        $this->info("Correcting user action plan advices.");
        $this->correctUserActionPlanAdvices();
        $this->info('Done!');
    }

    private function correctUserInterests()
    {
        $duplicates = DB::table('user_interests')
            ->selectRaw('user_id, input_source_id, interested_in_type, interested_in_id, count(*)')
            ->groupBy([
                'user_id',
                'input_source_id',
                'interested_in_type',
                'interested_in_id',
            ])
            ->having('count(*)', '>', 1)
            ->get();


        foreach ($duplicates as $duplicate) {
            // this way we can get the most recent duplicate row
            // we will keep this one as this is the most recent created row and probably wat the user actually wanted.
            $mostRecentDuplicateId = DB::table('user_interests')
                ->where('user_id', $duplicate->user_id)
                ->where('input_source_id', $duplicate->input_source_id)
                ->where('interested_in_id', $duplicate->interested_in_id)
                ->where('interested_in_type', $duplicate->interested_in_type)
                ->max('id');

            DB::table('user_interests')
                ->where('user_id', $duplicate->user_id)
                ->where('input_source_id', $duplicate->input_source_id)
                ->where('interested_in_id', $duplicate->interested_in_id)
                ->where('interested_in_type', $duplicate->interested_in_type)
                ->where('id', '!=', $mostRecentDuplicateId)
                ->delete();
        }
    }
    
    private function correctUserActionPlanAdvices()
    {
        $duplicates = DB::table('user_action_plan_advices')
            ->selectRaw('user_id, input_source_id, measure_application_id, step_id, count(*)')
            ->groupBy([
                'user_id',
                'input_source_id',
                'measure_application_id',
                'step_id',
            ])
            ->having('count(*)', '>', 1)
            ->get();


        foreach ($duplicates as $duplicate) {
            // this way we can get the most recent duplicate row
            // we will keep this one as this is the most recent created row and probably wat the user actually wanted.
            $mostRecentDuplicateId = DB::table('user_action_plan_advices')
                ->where('user_id', $duplicate->user_id)
                ->where('input_source_id', $duplicate->input_source_id)
                ->where('step_id', $duplicate->step_id)
                ->where('measure_application_id', $duplicate->measure_application_id)
                ->max('id');

            DB::table('user_action_plan_advices')
                ->where('user_id', $duplicate->user_id)
                ->where('input_source_id', $duplicate->input_source_id)
                ->where('step_id', $duplicate->step_id)
                ->where('measure_application_id', $duplicate->measure_application_id)
                ->where('id', '!=', $mostRecentDuplicateId)
                ->delete();
        }
    }

    private function correctBuildingServices()
    {
        $duplicates = DB::table('building_services')
            ->selectRaw('building_id, input_source_id, service_id, count(*)')
            ->groupBy([
                'building_id',
                'input_source_id',
                'service_id',
            ])
            ->having('count(*)', '>', 1)
            ->get();


        foreach ($duplicates as $duplicate) {
            // get the duplicates for the specific building its inputsource and service

            $mostRecentDuplicateId = DB::table('building_services')
                ->where('building_id', $duplicate->building_id)
                ->where('input_source_id', $duplicate->input_source_id)
                ->where('service_id', $duplicate->service_id)
                ->max('id');

            DB::table('building_services')
                ->where('building_id', $duplicate->building_id)
                ->where('input_source_id', $duplicate->input_source_id)
                ->where('service_id', $duplicate->service_id)
                ->where('id', '!=', $mostRecentDuplicateId)
                ->delete();
        }
    }

    /** Wood elements are a different cup of tea, and currently has 0 duplicates */
    private function correctBuildingElementsExceptWoodElements()
    {
        // Get all the duplicate building elements, grouped on inputsource, elementid en building id
        $duplicates = DB::table('building_elements')
            ->selectRaw('building_id, input_source_id, element_id, count(*)')
            ->where('element_id', '!=', 8)
            ->groupBy([
                'building_id',
                'input_source_id',
                'element_id',
            ])
            ->having('count(*)', '>', 1)
            ->get();


        foreach ($duplicates as $duplicate) {
            // this way we can get the most recent duplicate row
            // we will keep this one as this is the most recent created row and probably wat the user actually wanted.
            $mostRecentDuplicateId = DB::table('building_elements')
                ->where('building_id', $duplicate->building_id)
                ->where('input_source_id', $duplicate->input_source_id)
                ->where('element_id', $duplicate->element_id)
                ->max('id');

            // deleted the duplicates
            DB::table('building_elements')
                ->where('building_id', $duplicate->building_id)
                ->where('input_source_id', $duplicate->input_source_id)
                ->where('element_id', $duplicate->element_id)
                ->where('id', '!=', $mostRecentDuplicateId)
                ->delete();
        }

    }
}
