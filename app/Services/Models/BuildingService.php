<?php

namespace App\Services\Models;

use App\Events\BuildingAddressUpdated;
use App\Events\NoMappingFoundForBagMunicipality;
use App\Helpers\MappingHelper;
use App\Helpers\ToolQuestionHelper;
use App\Models\Building;
use App\Models\InputSource;
use App\Models\Municipality;
use App\Models\Scan;
use App\Services\Lvbag\BagService;
use App\Services\MappingService;
use App\Services\WoonplanService;
use App\Traits\FluentCaller;
use Illuminate\Support\Arr;
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
     * Method speaks for itself... however ->
     * The var is prefixed with "request", this is because we want ALL request address data.
     * (postal_code, number, extension, city, street)
     * We simply cant rely on external api data, the address data will always be filled with data from the request
     * we only need it for the id's
     * @param  array  $requestAddressData
     * @return void
     */
    public function updateAddress(array $requestAddressData)
    {
        $addressData = BagService::init()->firstAddress(
            $requestAddressData['postal_code'], $requestAddressData['number'], $requestAddressData['extension']
        );
        // filter relevant data from the request
        $buildingData = Arr::only($requestAddressData, ['street', 'city', 'postal_code', 'number']);
        $buildingData['extension'] = $requestAddressData['extension'] ?? '';

        // the only data we really need from the bag
        $buildingData['bag_woonplaats_id'] = $addressData['bag_woonplaats_id'];
        $buildingData['bag_addressid'] = $addressData['bag_addressid'];

        $this->building->update($buildingData);
    }

    public function attachMunicipality()
    {
        // MUST be string! Empty string is ok.
        $bagWoonplaatsId = (string) $this->building->bag_woonplaats_id;

        $municipalityName = BagService::init()
            ->showCity($bagWoonplaatsId, ['expand' => 'true'])
            ->municipalityName();

        // its entirely possible that a municipality is not returned from the bag.
        if (! is_null($municipalityName)) {
            $mappingService = new MappingService();
            $municipality = $mappingService
                ->from($municipalityName)
                ->type(MappingHelper::TYPE_BAG_MUNICIPALITY)
                ->resolveTarget()
                ->first();


            if ($municipality instanceof Municipality) {
                $this->building->municipality()->associate($municipality)->save();
            } else {
                // so the target is not resolved, thats "fine". We will check if a empty mapping exists
                // if not we will create it
                if ($mappingService->from($municipalityName)->doesntExist()) {
                    NoMappingFoundForBagMunicipality::dispatch($municipalityName);
                }
                // remove the relationship.
                $this->building->municipality()->disassociate()->save();
            }
        }
        // in the end it doesnt matter if the user dis or associated a municipality
        // we have to refresh its advices.
        BuildingAddressUpdated::dispatch($this->building);
    }

    /**
     * Get the answer for a set of questions including the input source that made that answer.
     * The example building will be prioritized if available.
     * Note that this method does not perform evaluation!
     *
     * @param  \Illuminate\Support\Collection  $toolQuestions
     *
     * @return \Illuminate\Support\Collection
     */
    public function getSourcedAnswers(Collection $toolQuestions): Collection
    {
        $masterInputSource = InputSource::findByShort(InputSource::MASTER_SHORT);
        $exampleBuildingInputSource = InputSource::findByShort(InputSource::EXAMPLE_BUILDING);

        $ids = $toolQuestions->whereNull('save_in')->pluck('id')->toArray();

        // Get the tool question answers for custom values.
        $answersForTqa = collect();
        if ( ! empty($ids)) {
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
        if ( ! empty($ids)) {
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
                if ( ! empty($where)) {
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
