<?php

namespace App\Services\Cloning;

use App\Models\Building;
use App\Models\InputSource;
use App\Services\Cloning\Cloners\UserActionPlanAdviceTable;
use App\Traits\FluentCaller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class CloneDataService {

    use FluentCaller;

    public Building $building;
    public InputSource $inputSource;
    public InputSource $cloneableInputSource;

    public static array $tables = [
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
            // the CMA must run before the user_action_plan_advices, this is KEY!
        'custom_measure_applications',
        'questions_answers',
        'tool_question_answers',
        'tool_settings',
        'user_action_plan_advices',
        'user_energy_habits',

    ];

    public function __construct(Building $building, InputSource $inputSource, InputSource $cloneableInputSource)
    {
        $this->building = $building;
        $this->inputSource = $inputSource;
        $this->cloneableInputSource = $cloneableInputSource;
    }

    public function clone()
    {
        // the building feature is set upon registry, so we will delete it before we clone it to prevent duplicate results
        Log::debug("Removing building features before cloning for building {$this->building->id} input source {$this->inputSource->short}");
        DB::table('building_features')
            ->where('building_id', $this->building->id)
            ->where('input_source_id', $this->inputSource->id)
            ->delete();

        Log::debug("Cloning all tables... {$this->building->id}");
        foreach (self::$tables as $table) {
            $wheres[] = ['input_source_id', '=', $this->cloneableInputSource->id];

            if (Schema::hasColumn($table, 'user_id')) {
                $wheres[] = ['user_id', '=', $this->building->user_id];
            } else {
                $wheres[] = ['building_id', '=', $this->building->id];
            }

            // get the data from the input source that we want to clone
            $cloneableDatas = DB::table($table)->where($wheres)->get()->toArray();

            $clonerClass = "App\Services\Cloning\Cloners\\".Str::ucfirst(Str::camel(Str::singular($table))).'Table';
            $customClonerExists = class_exists($clonerClass, true);

            // sometimes there are edge cases, those will be solved by a cloner class.
            if ($customClonerExists) {
                $dataToClone = $clonerClass::init($cloneableDatas, $this->inputSource)->transFormCloneableData();
            } else {
                // now transform whatever needs to be transformed in order to be cloned properly
                $dataToClone = $this->transformCloneableData($cloneableDatas, $this->inputSource);
            }

            // clone ze data.
            DB::table($table)->insert($dataToClone);
            // reset the wheres for the next iteration
            $wheres = [];
        }
        Log::debug("Cloning done! {$this->building->id}");
    }

    public static function transformCloneableData(array $cloneableData, InputSource $inputSource): array
    {
        foreach ($cloneableData as $index => $data) {
            $data = (array) $data;
            $data['input_source_id'] = $inputSource->id;
            unset($data['id']);
            $cloneableData[$index] = $data;
        }
        return $cloneableData;
    }
}