<?php

namespace App\Helpers;

use App\Helpers\Models\CooperationMeasureApplicationHelper;
use App\Scopes\GetValueScope;
use App\Services\UserActionPlanAdviceService;
use App\Services\Verbeterjehuis\RegulationService;
use Illuminate\Support\Collection;

class MyRegulationHelper
{
    public static function getRelevantRegulations($building, $inputSource): array
    {
        $relevantRegulations = [];
        $payload = RegulationService::init()
            ->forBuilding($building)
            ->getSearch();

        // here we will heavy modify the "payload" (regulations)
        // this is all bussines logic
        // we will filter out all the regulations that are not relevant for the user, they are not relevant when theere are no matching advices
        // we will also add the appropriate data while at it, so we dont have to do it again in the view.

        // first we have to get all available mappings for the user its action plan advices
        // first get all user action plan advices that have an advisable mapping
        /** @var Collection $advicesWithAdvisableMapping */
        $advicesWithAdvisableMapping = $building
            ->user
            ->userActionPlanAdvices()
            ->forInputSource($inputSource)
            ->cooperationMeasureForType(CooperationMeasureApplicationHelper::SMALL_MEASURE, $inputSource)
            ->join('mappings', 'mappings.from_model_id', '=', 'user_action_plan_advices.user_action_plan_advisable_id')
            // so the advisable MAY have a input source id
            // this would the case for the custom measure application
            // since each input source has its own unique row, we already know we have the correct one when comming from the advices. (since thats filtered on input source already)
            ->with(['userActionPlanAdvisable' => function ($query) {
                $query->withoutGlobalScope(GetValueScope::class);
            }])
            ->whereIn('user_action_plan_advices.category', [
                UserActionPlanAdviceService::CATEGORY_TO_DO,
                UserActionPlanAdviceService::CATEGORY_LATER,
            ])
            ->selectRaw(
                'json_unquote(mappings.target_data->"$.Value") as target_data_value, 
                user_action_plan_advices.id,
                user_action_plan_advices.input_source_id,
                user_action_plan_advices.user_action_plan_advisable_id,
                user_action_plan_advices.user_action_plan_advisable_type,
                user_action_plan_advices.loan_available,
                user_action_plan_advices.subsidy_available'
            )
            ->orderByRaw('user_action_plan_advices.id, target_data_value')
            ->get();

        foreach ($payload->transformedPayload as $regulation) {
            // create an empty key, check further on will be cleaner that way.
            $regulation['advisable_names'] = [];
            $regulationType = $regulation['Type'];
            // so they are called "Tags" dont ask me why
            // these consists of measures, measures that are to be found in the getFilters endpoint.
            $tagsForRegulation = $regulation['Tags'];
            $relatedAdvices = $advicesWithAdvisableMapping;

            foreach ($tagsForRegulation as $tagForRegulation) {
                // the "other" type is not covered here, this is because there is no extra logic
                // all advices from the $advicesWithAdvisableMapping are relevant, as long as they match the "tags" with measures.
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
            if ( ! empty($regulation['advisable_names'])) {
                $relevantRegulations[$regulationType][] = $regulation;
            }
        }
        return $relevantRegulations;
    }
}