<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\HeatPumpCharacteristic
 *
 * @property int $id
 * @property string $heat_pump_configurable_type
 * @property int $heat_pump_configurable_id
 * @property int|null $tool_question_custom_value_id
 * @property string $scop
 * @property string $scop_tap_water
 * @property int $share_percentage_tap_water
 * @property int $costs
 * @property int $standard_power_kw
 * @property string $type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static Builder|HeatPumpCharacteristic forHeatPumpConfigurable(\Illuminate\Database\Eloquent\Model $configurable)
 * @method static Builder|HeatPumpCharacteristic forHeatingTemperature(\App\Models\ToolQuestionCustomValue $heatingTemperature)
 * @method static Builder|HeatPumpCharacteristic forToolQuestionCustomValue(\App\Models\ToolQuestionCustomValue $toolQuestionCustomValue)
 * @method static Builder|HeatPumpCharacteristic newModelQuery()
 * @method static Builder|HeatPumpCharacteristic newQuery()
 * @method static Builder|HeatPumpCharacteristic query()
 * @method static Builder|HeatPumpCharacteristic whereCosts($value)
 * @method static Builder|HeatPumpCharacteristic whereCreatedAt($value)
 * @method static Builder|HeatPumpCharacteristic whereHeatPumpConfigurableId($value)
 * @method static Builder|HeatPumpCharacteristic whereHeatPumpConfigurableType($value)
 * @method static Builder|HeatPumpCharacteristic whereId($value)
 * @method static Builder|HeatPumpCharacteristic whereScop($value)
 * @method static Builder|HeatPumpCharacteristic whereScopTapWater($value)
 * @method static Builder|HeatPumpCharacteristic whereSharePercentageTapWater($value)
 * @method static Builder|HeatPumpCharacteristic whereStandardPowerKw($value)
 * @method static Builder|HeatPumpCharacteristic whereToolQuestionCustomValueId($value)
 * @method static Builder|HeatPumpCharacteristic whereType($value)
 * @method static Builder|HeatPumpCharacteristic whereUpdatedAt($value)
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
