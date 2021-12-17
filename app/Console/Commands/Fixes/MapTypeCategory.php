<?php

namespace App\Console\Commands\Fixes;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MapTypeCategory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fixes:map-type-category';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $buildingTypes = DB::table('building_types')->get();

        foreach ($buildingTypes as $buildingType) {
            DB::table('building_features')
                ->where('building_type_id', $buildingType->id)
                ->update([
                    'building_type_category_id' => $buildingType->building_type_category_id,
                ]);
        }

        DB::table('tool_question_answers')
            ->where('tool_question_id', DB::table('tool_questions')->where('short', 'building-type-category')->first()->id)
            ->delete();
    }
}
