<?php

namespace App\Services;

use App\Models\Building;
use App\Models\InputSource;
use App\Traits\FluentCaller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

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
            // now transform whatever needs to be transformed in order to be cloned properly
            $dataToClone = $this->transformCloneableData($cloneableDatas);

            // clone ze data.
            DB::table($table)->insert($dataToClone);
            // reset the wheres for the next iteration
            $wheres = [];
        }
        Log::debug("Cloning done! {$this->building->id}");
    }

    public function transformCloneableData(array $cloneableData): array
    {
        foreach ($cloneableData as $index => $data) {
            $data = (array) $data;
            $data['input_source_id'] = $this->inputSource->id;
            unset($data['id']);
            $cloneableData[$index] = $data;
        }
        return $cloneableData;
    }

    public static function getOpposingInputSource(InputSource $inputSource): InputSource
    {
        // this method was intended to just return the oppoosing input source
        // resident -> coach
        // coach -> resident
        // however this would cause problems on the user_action_plan_advices in combination with the custom_measure_applications due to the way they work
        // there are multiple applications for the same application, we track them through a hash.
        // a coach would get the same appli
        return [
            InputSource::COACH_SHORT => InputSource::findByShort(InputSource::MASTER_SHORT)
        ][$inputSource->short];
    }
}