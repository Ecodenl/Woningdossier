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
        $categoryQuestion = DB::table('tool_questions')->where('short', 'building-type-category')->first();

        $buildingTypes = DB::table('building_types')->get();
        $buildingTypeCategoriesToFix = DB::table('building_type_categories')
            ->where('short', '!=', 'apartment')
            ->pluck('id')
            ->toArray();

        $allAnswers = DB::table('tool_question_answers')
            ->where('tool_question_id', $categoryQuestion->id)
            ->get();

        // First map all answers to a valid building type
        foreach ($allAnswers as $answer) {
            $buildingFeature = DB::table('building_features')
                ->where('input_source_id', $answer->input_source_id)
                ->where('building_id', $answer->building_id)
                ->first();

            if ($buildingFeature instanceof \stdClass) {
                $buildingTypeId = in_array($answer->answer, $buildingTypeCategoriesToFix)
                    ? $buildingTypes->where('building_type_category_id', $answer->answer)->first()->id
                    : $buildingFeature->building_type_id;

                DB::table('building_features')->where('id', $buildingFeature->id)
                    ->update([
                        'building_type_category_id' => $answer->answer,
                        'building_type_id' => $buildingTypeId,
                    ]);
            } else {
                $buildingTypeId = $buildingTypes->where('building_type_category_id', $answer->answer)->first()->id;

                DB::table('building_features')->insert([
                    'building_id' => $answer->building_id,
                    'input_source_id' => $answer->input_source_id,
                    'building_type_category_id' => $answer->answer,
                    'building_type_id' => $buildingTypeId,
                ]);
            }
        }

        // Then map all non-answered to a valid category
        foreach ($buildingTypes as $buildingType) {
            DB::table('building_features')
                ->where('building_type_id', $buildingType->id)
                ->whereNull('building_type_category_id')
                ->update([
                    'building_type_category_id' => $buildingType->building_type_category_id,
                ]);
        }

        // Delete answers
        DB::table('tool_question_answers')
            ->where('tool_question_id', $categoryQuestion->id)
            ->delete();

        return 0;
    }
}
