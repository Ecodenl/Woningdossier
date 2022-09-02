<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

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
