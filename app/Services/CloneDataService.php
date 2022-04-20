<?php

namespace App\Services;

use App\Models\InputSource;
use App\Models\User;
use App\Traits\FluentCaller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CloneDataService {

    use FluentCaller;

    public User $user;
    public InputSource $inputSource;
    public InputSource $cloneableInputSource;

    public static array $tables = [
        'building_appliances',
        'building_elements',
        'building_features',
        'building_heaters',
        'building_insulated_glazings',
        'building_paintwork_statuses',
        'building_pv_panels',
        'building_roof_types',
        'building_services',
        'building_ventilations',
        'completed_questionnaires',
        'completed_steps',
        'completed_sub_steps',
        'considerables',
        'custom_measure_applications',
        'devices',
        'file_storages',
        'notifications',
        'private_message_views',
        'questions_answers',
        'roles',
        'step_comments',
        'tool_question_answers',
        'tool_settings',
        'user_action_plan_advice_comments',
        'user_action_plan_advices',
        'user_energy_habits',
        'user_interests',
    ];

    public function __construct(User $user, InputSource $inputSource, InputSource $cloneableInputSource)
    {
        $this->user = $user;
        $this->inputSource = $inputSource;
        $this->cloneableInputSource = $cloneableInputSource;
    }

    public function clone()
    {
        foreach (self::$tables as $table) {
            $wheres[] = ['input_source_id', '=', $this->cloneableInputSource->id];

            if (Schema::hasColumn($table, 'user_id')) {
                $wheres[] = ['user_id', '=', $this->user->id];
            } else {
                $wheres[] = ['building_id', '=', $this->user->building->id];
            }

            // get the data from the input source that we want to clone
            $cloneableDatas = DB::table($table)->where($wheres)->get()->toArray();
            // now transform whatever needs to be transformed in order to be cloned properly
            $dataToClone = $this->transformCloneableData($cloneableDatas);

            // clone ze data.
            DB::table($table)->insert($dataToClone);
        }
    }

    public function transformCloneableData(array $cloneableData): array
    {
        foreach ($cloneableData as $index => $data) {
            $data = (array) $data;
            $cloneableData[$index] = $data['input_source_id'] = $this->inputSource->id;
        }
        return $cloneableData;
    }
}