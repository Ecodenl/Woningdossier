<?php

namespace App\Services\Cloning\Cloners;

use App\Models\CustomMeasureApplication;
use App\Models\InputSource;
use App\Services\Cloning\CloneDataService;
use App\Traits\FluentCaller;

class UserActionPlanAdviceTable extends Cloner implements DataTransformer
{
    use FluentCaller;

    public function transFormCloneableData(): array
    {
        // first we do the default transformation
        $this->data = CloneDataService::transformCloneableData($this->data, $this->inputSource);

        // and now do the extra
        foreach($this->data as $index => $data) {
            // this just sets the input source
            if ($data['user_action_plan_advisable_type'] === CustomMeasureApplication::class) {
                $this->data[$index] = $this->transform($data);
            }
        }

        return $this->data;
    }

    public function transform(array $data)
    {
        // we have to get the current input source its custom measure application
        // we will do this by retrieving the sibling of cloneable one.

        // we have to scope on ALL input sources because the advisable_id belongs to the input source we are cloning.
        $clonableCustomMeasureApplication = CustomMeasureApplication::allInputSources()->find($data['user_action_plan_advisable_id']);

        $customMeasureApplication = $clonableCustomMeasureApplication->getSibling($this->inputSource);

        $data['user_action_plan_advisable_id'] = $customMeasureApplication->id;

        return $data;
    }
}