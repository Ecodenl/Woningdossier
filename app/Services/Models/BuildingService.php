<?php

namespace App\Services\Models;

use App\Helpers\ToolQuestionHelper;
use App\Models\Building;
use App\Models\InputSource;
use App\Traits\FluentCaller;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class BuildingService
{
    use FluentCaller;
    
    public ?Building $building;
    
    public function __construct(Building $building)
    {
        $this->building = $building;
    }

    /**
     * Get the answer for a set of questions including the input source that made that answer.
     * Note that this method does not perform evaluation!
     *
     * @param \Illuminate\Support\Collection $toolQuestions
     *
     * @return \Illuminate\Support\Collection
     */
    public function getSourcedAnswers(Collection $toolQuestions): Collection
    {
        $masterInputSource = InputSource::findByShort(InputSource::MASTER_SHORT);

        $ids = $toolQuestions->whereNull('save_in')->pluck('id')->toArray();

        // Get the tool question answers for custom values.
        $answersForTqa = collect();
        if (! empty($ids)) {
            // This sub-query selects the latest input source that made changes on the tool question.
            $selectQuery = DB::table('tool_question_answers')
                ->select('input_source_id')
                ->where('building_id', $this->building->id)
                ->whereRaw('tool_question_id = tqa.tool_question_id')
                ->whereRaw('answer = tqa.answer')
                ->where('input_source_id', '!=', $masterInputSource->id)
                ->orderByDesc('updated_at')
                ->limit(1);

            // https://laravel.com/docs/9.x/queries#subquery-joins
            // This sub-join retrieves all non-master answers.
            $subQuery = DB::table('tool_question_answers')
                ->select('building_id', 'input_source_id', 'tool_question_id', 'answer')
                ->where('building_id', $this->building->id)
                ->where('input_source_id', '!=', $masterInputSource->id)
                ->groupBy('building_id', 'input_source_id', 'tool_question_id', 'answer');

            // Finally, we select the relevant data, where the input source is the latest changed.
            // We convert all "under-the-hood" stdClasses to arrays.
            $answersForTqa = DB::table('tool_question_answers as tqa')
                ->select('tqa.tool_question_id', 'tqa.answer', 'nma.input_source_id', 'is.name as input_source_name')
                ->selectSub($selectQuery, 'latest_source_id')
                ->where('tqa.building_id', $this->building->id)
                ->where('tqa.input_source_id', $masterInputSource->id)
                ->whereIn('tqa.tool_question_id', $ids)
                ->leftJoinSub($subQuery, 'nma', function ($join) {
                    $join->on('tqa.building_id', '=', 'nma.building_id')
                        ->on('tqa.tool_question_id', '=', 'nma.tool_question_id')
                        ->on('tqa.answer', '=', 'nma.answer');
                })
                ->leftJoin('input_sources as is', 'nma.input_source_id', '=', 'is.id')
                ->havingRaw('nma.input_source_id = latest_source_id')
                ->get()
                ->groupBy('tool_question_id')
                ->map(fn ($val) => $val->map(fn ($subVal) => (array) $subVal)->toArray());
        }

        $ids = $toolQuestions->whereNotNull('save_in')->pluck('id')->toArray();

        // Get the tool question answers for save in questions.
        $answersForSaveIn = collect();
        if (! empty($ids)) {
            $saveIns = $toolQuestions->whereNotNull('save_in')->pluck('save_in', 'id')->toArray();
            foreach ($saveIns as $toolQuestionId => $saveIn) {
                $resolved = ToolQuestionHelper::resolveSaveIn($saveIn, $this->building);
                // Note: Where could contain extra queryable, e.g. service. If empty, the query builder
                // will safely discard the where statement. If not empty, it gets added to the query.
                $where = $resolved['where'];
                $table = $resolved['table'];
                $answerColumn = $resolved['column'];
                $whereColumn = array_key_exists('user_id', $where) ? 'user_id' : 'building_id';
                $value = data_get($resolved, "where.{$whereColumn}");
                unset($where[$whereColumn]);

                // This sub-query selects the latest input source that made changes on the tool question.
                $selectQuery = DB::table($table)
                    ->select('input_source_id')
                    ->where($whereColumn, $value)
                    ->whereRaw("{$answerColumn} = tbl.{$answerColumn}")
                    ->where('input_source_id', '!=', $masterInputSource->id)
                    ->where($where)
                    ->orderByDesc('updated_at')
                    ->limit(1);

                // This sub-join retrieves all non-master answers.
                $subQuery = DB::table($table)
                    ->select($whereColumn, 'input_source_id', $answerColumn)
                    ->where($whereColumn, $value)
                    ->where($where)
                    ->where('input_source_id', '!=', $masterInputSource->id)
                    ->groupBy($whereColumn, 'input_source_id', $answerColumn);

                // Finally, we select the relevant data, where the input source is the latest changed.
                // We convert all "under-the-hood" stdClasses to arrays.
                $answersForResolved = DB::table("{$table} as tbl")
                    ->select("tbl.{$answerColumn} as answer", 'nma.input_source_id', 'is.name as input_source_name')
                    ->selectSub($selectQuery, 'latest_source_id')
                    ->where("tbl.{$whereColumn}", $value)
                    ->where($where)
                    ->where('tbl.input_source_id', $masterInputSource->id)
                    ->leftJoinSub($subQuery, 'nma', function ($join) use ($whereColumn, $answerColumn) {
                        $join->on("tbl.{$whereColumn}", '=', "nma.{$whereColumn}")
                            ->on("tbl.{$answerColumn}", '=', "nma.{$answerColumn}");
                    })
                    ->leftJoin('input_sources as is', 'nma.input_source_id', '=', 'is.id')
                    ->havingRaw('nma.input_source_id = latest_source_id')
                    ->get()
                    ->map(fn ($val) => (array) $val)
                    ->toArray();

                $answersForSaveIn->put($toolQuestionId, $answersForResolved);
            }
        }

        return $answersForTqa->union($answersForSaveIn);
    }


    public static function deleteBuilding(Building $building)
    {
        $building->completedSteps()->withoutGlobalScopes()->delete();
        // delete the private messages from the cooperation
        $building->privateMessages()->withoutGlobalScopes()->delete();

        $building->stepComments()->withoutGlobalScopes()->delete();

        // table will be removed anyways.
        \DB::table('building_appliances')->whereBuildingId($building->id)->delete();

        $building->forceDelete();
    }
}
