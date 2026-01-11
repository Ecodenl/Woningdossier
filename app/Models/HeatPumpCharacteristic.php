<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\HeatPumpCharacteristic
 *
 * @property int $id
 * @property string $heat_pump_configurable_type
 * @property int $heat_pump_configurable_id
 * @property int|null $tool_question_custom_value_id
 * @property numeric $scop
 * @property numeric $scop_tap_water
 * @property int $share_percentage_tap_water
 * @property int $costs
 * @property int $standard_power_kw
 * @property string $type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static Builder<static>|HeatPumpCharacteristic forHeatPumpConfigurable(\Illuminate\Database\Eloquent\Model $configurable)
 * @method static Builder<static>|HeatPumpCharacteristic forToolQuestionCustomValue(\App\Models\ToolQuestionCustomValue $toolQuestionCustomValue)
 * @method static Builder<static>|HeatPumpCharacteristic newModelQuery()
 * @method static Builder<static>|HeatPumpCharacteristic newQuery()
 * @method static Builder<static>|HeatPumpCharacteristic query()
 * @method static Builder<static>|HeatPumpCharacteristic whereCosts($value)
 * @method static Builder<static>|HeatPumpCharacteristic whereCreatedAt($value)
 * @method static Builder<static>|HeatPumpCharacteristic whereHeatPumpConfigurableId($value)
 * @method static Builder<static>|HeatPumpCharacteristic whereHeatPumpConfigurableType($value)
 * @method static Builder<static>|HeatPumpCharacteristic whereId($value)
 * @method static Builder<static>|HeatPumpCharacteristic whereScop($value)
 * @method static Builder<static>|HeatPumpCharacteristic whereScopTapWater($value)
 * @method static Builder<static>|HeatPumpCharacteristic whereSharePercentageTapWater($value)
 * @method static Builder<static>|HeatPumpCharacteristic whereStandardPowerKw($value)
 * @method static Builder<static>|HeatPumpCharacteristic whereToolQuestionCustomValueId($value)
 * @method static Builder<static>|HeatPumpCharacteristic whereType($value)
 * @method static Builder<static>|HeatPumpCharacteristic whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class HeatPumpCharacteristic extends Model
{
    const string TYPE_HYBRID = 'hybrid';
    const string TYPE_FULL = 'full';

    #[Scope]
    protected function forToolQuestionCustomValue(
        Builder $query,
        ToolQuestionCustomValue $toolQuestionCustomValue
    ): Builder
    {
        return $query->where(
            'tool_question_custom_value_id',
            '=',
            $toolQuestionCustomValue->id
        );
    }

    #[Scope]
    protected function forHeatPumpConfigurable(
        Builder $query,
        Model $configurable
    ): Builder
    {
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
