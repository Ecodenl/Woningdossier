<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\KeyFigureInsulationFactor
 *
 * @property int $id
 * @property numeric $insulation_grade
 * @property numeric $insulation_factor
 * @property int $energy_consumption_per_m2
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static Builder<static>|KeyFigureInsulationFactor newModelQuery()
 * @method static Builder<static>|KeyFigureInsulationFactor newQuery()
 * @method static Builder<static>|KeyFigureInsulationFactor query()
 * @method static Builder<static>|KeyFigureInsulationFactor whereCreatedAt($value)
 * @method static Builder<static>|KeyFigureInsulationFactor whereEnergyConsumptionPerM2($value)
 * @method static Builder<static>|KeyFigureInsulationFactor whereId($value)
 * @method static Builder<static>|KeyFigureInsulationFactor whereInsulationFactor($value)
 * @method static Builder<static>|KeyFigureInsulationFactor whereInsulationGrade($value)
 * @method static Builder<static>|KeyFigureInsulationFactor whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class KeyFigureInsulationFactor extends Model
{

    protected function casts(): array
    {
        return [
            'insulation_grade' => 'decimal:2',
            'insulation_factor' => 'decimal:2',
        ];
    }

    #[Scope]
    protected function forInsulationFactor(Builder $query, $factor): Builder
    {
        $factor = number_format($factor, 2, '.', '');

        return $query->where('insulation_factor', '=', $factor);
    }
}
