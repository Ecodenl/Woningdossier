<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class HeatPumpCharacteristic extends Model
{
    const TYPE_HYBRID = 'hybrid';
    const TYPE_FULL = 'full';

    public function scopeForHeatingTemperature(
        Builder $query,
        ToolQuestionCustomValue $heatingTemperature
    ) {
        return $this->scopeForToolQuestionCustomValue(
            $query,
            $heatingTemperature
        );
    }

    public function scopeForToolQuestionCustomValue(
        Builder $query,
        ToolQuestionCustomValue $toolQuestionCustomValue
    ) {
        return $query->where(
            'tool_question_custom_value_id',
            '=',
            $toolQuestionCustomValue->id
        );
    }

    public function scopeForHeatPumpConfigurable(
        Builder $query,
        Model $configurable
    ) {
        return $query->where(
            'heat_pump_configurable_type',
            '=',
            get_class($configurable)
        )
                     ->where(
                         'heat_pump_configurable_id',
                         '=',
                         $configurable->id
                     );
    }
}
