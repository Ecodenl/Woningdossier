<?php

namespace App\Services\Models;

use App\Events\BuildingAppointmentDateUpdated;
use App\Helpers\KengetallenCodes;
use App\Helpers\ToolQuestionHelper;
use App\Jobs\CheckBuildingAddress;
use App\Jobs\RefreshRegulationsForBuildingUser;
use App\Models\Building;
use App\Models\CustomMeasureApplication;
use App\Models\InputSource;
use App\Models\Scan;
use App\Models\ToolQuestion;
use App\Services\Kengetallen\KengetallenService;
use App\Services\Kengetallen\Resolvers\RvoDefined;
use App\Services\ToolQuestionService;
use App\Services\WoonplanService;
use App\Traits\FluentCaller;
use App\Traits\Services\HasBuilding;
use App\Traits\Services\HasInputSources;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BuildingService
{
    use FluentCaller,
        HasBuilding,
        HasInputSources;

    public function __construct(?Building $building = null)
    {
        $this->building = $building;
    }

    /**
     * convenient way of setting a appointment date on a building.
     *
     * @param  string
     */
    public function setAppointmentDate($appointmentDate): void
    {
        $this->building->buildingStatuses()->create([
            'status_id' => $this->building->getMostRecentBuildingStatus()->status_id,
            'appointment_date' => $appointmentDate,
        ]);
        Log::debug(__METHOD__);
        Log::debug("dispatching BuildingAppointmentDateUpdated for building {$this->building->id}");

        BuildingAppointmentDateUpdated::dispatch($this->building);
    }

    /**
     * This method will set the BuildingDefined kengetallen to the "default" (RvoDefined) kengetallen
     */
    public function setBuildingDefinedKengetallen(): void
    {
        $kengetallenService = app(KengetallenService::class)->forBuilding($this->building);
        $toolQuestionService = ToolQuestionService::init()
            ->building($this->building)
            ->currentInputSource(InputSource::resident());

        // force to resolve through the code defined kengetallen.
        $toolQuestionService
            ->toolQuestion(ToolQuestion::findByShort('electricity-price-euro'))
            ->save($kengetallenService->get(new RvoDefined(), KengetallenCodes::EURO_SAVINGS_ELECTRICITY));

        $toolQuestionService
            ->toolQuestion(ToolQuestion::findByShort('gas-price-euro'))
            ->save($kengetallenService->get(new RvoDefined(), KengetallenCodes::EURO_SAVINGS_GAS));
    }

    public function canCalculate(Scan $scan): bool
    {
        if ($scan->isQuickScan()) {
            $quickScan = Scan::findByShort(Scan::QUICK);
            $woonplanService = WoonplanService::init($this->building)->scan($quickScan);
            // iknow, the variable is not needed.
            // just got it here as a description since this same statement is found in different context.
            $canRecalculate = $woonplanService->buildingCompletedFirstFourSteps() || $woonplanService->buildingHasMeasureApplications();
            return $canRecalculate;
        }

        if ($scan->isLiteScan()) {
            return $this->building->hasCompletedScan($scan, InputSource::findByShort(InputSource::MASTER_SHORT));
        }
    }

    /**
     * Get the answer for a set of questions including the input source that made that answer.
     * The example building will be prioritized if available.
     * Note that this method does not perform evaluation!
     */
    public function getSourcedAnswers(Collection $toolQuestions): Collection
    {
        $masterInputSource = InputSource::findByShort(InputSource::MASTER_SHORT);
        $exampleBuildingInputSource = InputSource::findByShort(InputSource::EXAMPLE_BUILDING_SHORT);

        $ids = $toolQuestions->whereNull('save_in')->pluck('id')->toArray();

        // Get the tool question answers for custom values.
        $answersForTqa = collect();
        if (! empty($ids)) {
            // This sub-query selects the example building if it has the same answer, or else the latest input source
            // that made changes on the tool question.
            $selectQuery = "(SELECT IF (
                (
                    SELECT COUNT(*) FROM tool_question_answers
                    WHERE building_id = ? AND tool_question_id = tqa.tool_question_id AND answer = tqa.answer
                    AND input_source_id = ?
                ) > 0, ?, (
                    SELECT input_source_id FROM tool_question_answers
                    WHERE building_id = ? AND tool_question_id = tqa.tool_question_id AND answer = tqa.answer
                    AND input_source_id != ? ORDER BY updated_at DESC LIMIT 1
                )
            ) AS latest_source_id)";

            // https://laravel.com/docs/9.x/queries#subquery-joins
            // This sub-join retrieves all non-master answers.
            $subQuery = DB::table('tool_question_answers')
                ->select('building_id', 'input_source_id', 'tool_question_id', 'answer')
                ->where('building_id', $this->building->id)
                ->where('input_source_id', '!=', $masterInputSource->id)
                ->groupBy('building_id', 'input_source_id', 'tool_question_id', 'answer');

            // Finally, we select the relevant data, where the input source is the latest changed.
            // We convert all "under-the-hood" stdClasses to arrays.
            $answersForTqa = DB::table('tool_question_answers AS tqa')
                ->select('tqa.tool_question_id', 'tqa.answer', 'nma.input_source_id', 'is.name AS input_source_name')
                ->selectSub(
                    fn($query) => $query->selectRaw($selectQuery, [
                        $this->building->id,
                        $exampleBuildingInputSource->id,
                        $exampleBuildingInputSource->id,
                        $this->building->id,
                        $masterInputSource->id
                    ]),
                    'latest_source_id'
                )
                ->where('tqa.building_id', $this->building->id)
                ->where('tqa.input_source_id', $masterInputSource->id)
                ->whereIn('tqa.tool_question_id', $ids)
                ->leftJoinSub($subQuery, 'nma', function ($join) {
                    $join->on('tqa.building_id', '=', 'nma.building_id')
                        ->on('tqa.tool_question_id', '=', 'nma.tool_question_id')
                        ->on('tqa.answer', '=', 'nma.answer');
                })
                ->leftJoin('input_sources AS is', 'nma.input_source_id', '=', 'is.id')
                ->havingRaw('nma.input_source_id = latest_source_id')
                ->get()
                ->groupBy('tool_question_id')
                ->map(fn($val) => $val->map(fn($subVal) => (array)$subVal)->toArray());
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

                $append = '';
                if (! empty($where)) {
                    foreach ($where as $col => $val) {
                        $append .= " AND {$col} = {$val}";
                    }
                }

                // This sub-query selects the example building if it has the same answer, or else the latest input source
                // that made changes on the tool question.
                $selectQuery = "(SELECT IF (
                    (
                        SELECT COUNT(*) FROM {$table}
                        WHERE {$whereColumn} = ? AND {$answerColumn} = tbl.{$answerColumn}
                        AND input_source_id = ? {$append}
                    ) > 0, ?, (
                        SELECT input_source_id FROM {$table}
                        WHERE {$whereColumn} = ? AND {$answerColumn} = tbl.{$answerColumn}
                        AND input_source_id != ? {$append} ORDER BY updated_at DESC LIMIT 1
                    )
                ) AS latest_source_id)";

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
                    ->select("tbl.{$answerColumn} AS answer", 'nma.input_source_id', 'is.name AS input_source_name')
                    ->selectSub(
                        fn($query) => $query->selectRaw($selectQuery, [
                            $value,
                            $exampleBuildingInputSource->id,
                            $exampleBuildingInputSource->id,
                            $value,
                            $masterInputSource->id
                        ]),
                        'latest_source_id'
                    )
                    ->where("tbl.{$whereColumn}", $value)
                    ->where($where)
                    ->where('tbl.input_source_id', $masterInputSource->id)
                    ->leftJoinSub($subQuery, 'nma', function ($join) use ($whereColumn, $answerColumn) {
                        $join->on("tbl.{$whereColumn}", '=', "nma.{$whereColumn}")
                            ->on("tbl.{$answerColumn}", '=', "nma.{$answerColumn}");
                    })
                    ->leftJoin('input_sources AS is', 'nma.input_source_id', '=', 'is.id')
                    ->havingRaw('nma.input_source_id = latest_source_id')
                    ->get()
                    ->map(fn($val) => (array)$val)
                    ->toArray();

                $answersForSaveIn->put($toolQuestionId, $answersForResolved);
            }
        }

        return $answersForTqa->union($answersForSaveIn);
    }

    public function performMunicipalityCheck()
    {
        $building = $this->building;

        $currentMunicipality = $building->municipality_id;
        CheckBuildingAddress::dispatchSync($building, $this->inputSource);
        // Get a fresh (and updated) building instance
        $building = $building->fresh();
        $newMunicipality = $building->municipality_id;

        // Check if a municipality was attached. If not, dispatch it on the regular queue so it retries.
        // If the CheckBuildingAddress attaches a municipality, the BuildingAddressUpdated will be fired from the attachMunicipality method.
        // This event has a RefreshBuildingUserHisAdvices listener that calls the RefreshRegulationsForBuildingUser job.
        // If the municipality hasn't changed, however, we will manually dispatch a refresh.
        if (is_null($newMunicipality)) {
            CheckBuildingAddress::dispatch($building, $this->inputSource);
        } elseif ($currentMunicipality === $newMunicipality) {
            RefreshRegulationsForBuildingUser::dispatch($building);
        }
    }

    public static function deleteBuilding(Building $building)
    {
        $building->completedSteps()->withoutGlobalScopes()->delete();
        // delete the private messages from the cooperation
        $building->privateMessages()->withoutGlobalScopes()->delete();

        $building->stepComments()->withoutGlobalScopes()->delete();

        // Remove all mappings related to custom measure applications. Custom measures will get "auto deleted" due
        // to cascade on delete, but mappings are a morph relation without foreign key constraints, so we must
        // delete them ourselves.
        DB::table('mappings')->where('from_model_type', CustomMeasureApplication::class)
            ->whereIn(
                'from_model_id',
                $building->customMeasureApplications()->allInputSources()->pluck('id')->toArray()
            )
            ->delete();

        // table will be removed anyways.
        DB::table('building_appliances')->whereBuildingId($building->id)->delete();

        $building->forceDelete();
    }
}
