<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\KeyFigureHeatPumpCoverage
 *
 * @property int $id
 * @property string $betafactor
 * @property int $tool_question_custom_value_id
 * @property int $percentage
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static Builder|KeyFigureHeatPumpCoverage forBetaFactor($betafactor)
 * @method static Builder|KeyFigureHeatPumpCoverage forHeatingTemperature(\App\Models\ToolQuestionCustomValue $heatingTemperature)
 * @method static Builder|KeyFigureHeatPumpCoverage forToolQuestionCustomValue(\App\Models\ToolQuestionCustomValue $toolQuestionCustomValue)
 * @method static Builder|KeyFigureHeatPumpCoverage newModelQuery()
 * @method static Builder|KeyFigureHeatPumpCoverage newQuery()
 * @method static Builder|KeyFigureHeatPumpCoverage query()
 * @method static Builder|KeyFigureHeatPumpCoverage whereBetafactor($value)
 * @method static Builder|KeyFigureHeatPumpCoverage whereCreatedAt($value)
 * @method static Builder|KeyFigureHeatPumpCoverage whereId($value)
 * @method static Builder|KeyFigureHeatPumpCoverage wherePercentage($value)
 * @method static Builder|KeyFigureHeatPumpCoverage whereToolQuestionCustomValueId($value)
 * @method static Builder|KeyFigureHeatPumpCoverage whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class KeyFigureHeatPumpCoverage extends Model
{

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

    public function scopeForBetaFactor(Builder $query, $betafactor)
    {
        $round = floor($betafactor * 10) / 10;

        return $query->where('betafactor', '<=', $betafactor)
                     ->where('betafactor', '>=', $round);
    }
}
