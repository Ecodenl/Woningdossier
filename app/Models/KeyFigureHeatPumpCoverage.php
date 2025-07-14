<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\KeyFigureHeatPumpCoverage
 *
 * @property int $id
 * @property numeric $betafactor
 * @property int $tool_question_custom_value_id
 * @property int $percentage
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static Builder<static>|KeyFigureHeatPumpCoverage newModelQuery()
 * @method static Builder<static>|KeyFigureHeatPumpCoverage newQuery()
 * @method static Builder<static>|KeyFigureHeatPumpCoverage query()
 * @method static Builder<static>|KeyFigureHeatPumpCoverage whereBetafactor($value)
 * @method static Builder<static>|KeyFigureHeatPumpCoverage whereCreatedAt($value)
 * @method static Builder<static>|KeyFigureHeatPumpCoverage whereId($value)
 * @method static Builder<static>|KeyFigureHeatPumpCoverage wherePercentage($value)
 * @method static Builder<static>|KeyFigureHeatPumpCoverage whereToolQuestionCustomValueId($value)
 * @method static Builder<static>|KeyFigureHeatPumpCoverage whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class KeyFigureHeatPumpCoverage extends Model
{

    protected function casts(): array
    {
        return [
            'betafactor' => 'decimal:2',
        ];
    }

    #[Scope]
    protected function forHeatingTemperature(
        Builder $query,
        ToolQuestionCustomValue $heatingTemperature
    ): Builder
    {
        return $this->scopeForToolQuestionCustomValue(
            $query,
            $heatingTemperature
        );
    }

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
    protected function forBetaFactor(Builder $query, $betafactor): Builder
    {
        $round = floor($betafactor * 10) / 10;

        return $query->where('betafactor', '<=', $betafactor)
            ->where('betafactor', '>=', $round);
    }
}
