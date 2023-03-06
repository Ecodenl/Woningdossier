<?php

namespace App\Services\Econobis\Payloads;

use App\Models\InputSource;
use App\Models\UserActionPlanAdvice;
use App\Services\UserActionPlanAdviceService;

class WoonplanPayload extends EconobisPayload
{
    public function buildPayload(): array
    {
        $building = $this->building;

        $inputSource = InputSource::findByShort(InputSource::MASTER_SHORT);

        $advices = $building
            ->user
            ->userActionPlanAdvices()
            ->forInputSource($inputSource)
            ->category(UserActionPlanAdviceService::CATEGORY_TO_DO)
            ->get();

        /** @var UserActionPlanAdvice $advice */
        foreach ($advices as $advice) {
            $advisable = $advice->userActionPlanAdvisable()->forInputSource($inputSource)->first();

            $data['user_action_plan_advices'][] = [
                'measure_id' => $advice->user_action_plan_advisable_id,
                'measure_type' => $advice->user_action_plan_advisable_type,
                'name' => $advisable->measure_name ?? $advisable->name,
                'surface' => ''
            ];
        }

        dd($data);
        return $data;
    }
}