<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\HeatPumpCharacteristic
 *
 * @method static Builder|HeatPumpCharacteristic forHeatPumpConfigurable(\Illuminate\Database\Eloquent\Model $configurable)
 * @method static Builder|HeatPumpCharacteristic forHeatingTemperature(\App\Models\ToolQuestionCustomValue $heatingTemperature)
 * @method static Builder|HeatPumpCharacteristic forToolQuestionCustomValue(\App\Models\ToolQuestionCustomValue $toolQuestionCustomValue)
 * @method static Builder|HeatPumpCharacteristic newModelQuery()
 * @method static Builder|HeatPumpCharacteristic newQuery()
 * @method static Builder|HeatPumpCharacteristic query()
 * @mixin \Eloquent
 */
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
