<?php

namespace App\Helpers;

use App\Helpers\Models\CooperationMeasureApplicationHelper;
use App\Models\CooperationMeasureApplication;
use App\Models\CustomMeasureApplication;
use App\Models\MeasureApplication;
use App\Models\MeasureCategory;
use App\Scopes\GetValueScope;
use App\Services\UserActionPlanAdviceService;
use App\Services\Verbeterjehuis\Payloads\Search;
use App\Services\Verbeterjehuis\RegulationService;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Collection;

class MyRegulationHelper
{
    public static function getRelevantRegulations($building, $inputSource): array
    {
        $relevantRegulations = [];
        $payload = Wrapper::wrapCall(fn () => RegulationService::init()
            ->forBuilding($building)
            ->getSearch());

        // Here we will heavily modify the "payload" (regulations).
        // This is all business logic:
        // - we will filter out all the regulations that are not relevant for the user, they are not relevant when there are no matching advices.
        // - we will also add the appropriate data while at it, so we don't have to do it again in the view.

        // First we have to get all available mappings for the user its action plan advices.
        // First get all user action plan advices that have an advisable mapping.
        if ($payload instanceof Search) {
            $selectColumns = [
                'user_action_plan_advices.id',
                'user_action_plan_advices.input_source_id',
                'user_action_plan_advices.user_action_plan_advisable_id',
                'user_action_plan_advices.user_action_plan_advisable_type',
                'user_action_plan_advices.loan_available',
                'user_action_plan_advices.subsidy_available',
            ];

            $baseQuery = $building
                ->user
                ->userActionPlanAdvices()
                ->forInputSource($inputSource)
                ->cooperationMeasureForType(CooperationMeasureApplicationHelper::SMALL_MEASURE, $inputSource)
                // So the advisable MAY have a input source id.
                // This would the case for the custom measure application.
                // Since each input source has its own unique row, we already know we have the correct one when coming
                // from the advices (since that's filtered on input source already).
                ->with([
                    'userActionPlanAdvisable' => function ($query) {
                        $query->withoutGlobalScope(GetValueScope::class);
                    },
                ])
                ->whereIn('user_action_plan_advices.category', [
                    UserActionPlanAdviceService::CATEGORY_TO_DO,
                    UserActionPlanAdviceService::CATEGORY_LATER,
                ]);

            // first we will retrieve the advisable mappings for the regular measure applications.
            $selectRaw = implode(', ', array_merge($selectColumns, [
                'json_unquote(end_mapping.target_data->"$.Value") as target_data_value',
            ]));
            $advicesWithAdvisableMappingForMeasureApplications = $baseQuery
                ->clone()
                ->selectRaw($selectRaw)
                ->join('mappings as end_mapping', function (JoinClause $join) {
                    $join->on('end_mapping.from_model_id', '=',
                        'user_action_plan_advices.user_action_plan_advisable_id')
                        ->where('end_mapping.from_model_type', MeasureApplication::class);
                })
                ->where('user_action_plan_advices.user_action_plan_advisable_type', MeasureApplication::class)
                ->get();

            // Now we will do the same, except for the custom and cooperation measure applications.
            // These differ, as they are connected to a measureCategory. That measureCategory is mapped to
            // the actual vbjehuis measure.
            $selectRaw = implode(',', array_merge($selectColumns, [
                'json_unquote(end_mapping.target_data->"$.Value") as target_data_value',
            ]));
            $advicesWithAdvisableMappingForMeasureCategoryRelated = $baseQuery
                ->clone()
                ->selectRaw($selectRaw)
                ->join('mappings', function (JoinClause $join) {
                    $join->on('mappings.from_model_id', '=', 'user_action_plan_advices.user_action_plan_advisable_id')
                        ->whereRaw('mappings.from_model_type = user_action_plan_advices.user_action_plan_advisable_type');
                })
                ->leftJoin('mappings as end_mapping', function (JoinClause $join) {
                    $join->on('mappings.target_model_id', '=', 'end_mapping.from_model_id')
                        ->where('end_mapping.from_model_type', MeasureCategory::class);
                })
                ->whereIn(
                    'user_action_plan_advices.user_action_plan_advisable_type',
                    [CustomMeasureApplication::class, CooperationMeasureApplication::class]
                )
                ->get();

            /** @var Collection $advicesWithAdvisableMapping */
            $advicesWithAdvisableMapping = $advicesWithAdvisableMappingForMeasureCategoryRelated
                ->merge($advicesWithAdvisableMappingForMeasureApplications);

            $transformedPayload = $payload->forBuildingContractType($building, $inputSource)->all();

            foreach ($transformedPayload as $regulation) {
                // create an empty key, check further on will be cleaner that way.
                $regulation['advisable_names'] = [];
                $regulationType = $regulation['Type'];
                // so they are called "Tags" dont ask me why
                // these consists of measures, measures that are to be found in the getFilters endpoint.
                $tagsForRegulation = $regulation['Tags'];
                $relatedAdvices = $advicesWithAdvisableMapping;

                foreach ($tagsForRegulation as $tagForRegulation) {
                    // All advices from the $advicesWithAdvisableMapping are relevant, as long
                    // as they match the "tags" with measures.
                    if ($regulationType == RegulationService::OTHER) {
                        $relatedAdvices = $advicesWithAdvisableMapping;
                    }
                    if ($regulationType == RegulationService::LOAN) {
                        $relatedAdvices = $advicesWithAdvisableMapping->where('loan_available', true);
                    }
                    if ($regulationType == RegulationService::SUBSIDY) {
                        $relatedAdvices = $advicesWithAdvisableMapping->where('subsidy_available', true);
                    }

                    $relatedAdvices = $relatedAdvices->where('target_data_value', $tagForRegulation['Value']);

                    foreach ($relatedAdvices as $relatedAdvice) {
                        // the morph relation.
                        $advisable = $relatedAdvice->userActionPlanAdvisable;
                        $regulation['advisable_names'][] = $advisable->name ?? $advisable->measure_name;
                    }
                }
                if (! empty($regulation['advisable_names'])) {
                    $relevantRegulations[$regulationType][] = $regulation;
                }
            }
        }

        return $relevantRegulations;
    }
}
