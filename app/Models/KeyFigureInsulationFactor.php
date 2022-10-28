<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\KeyFigureInsulationFactor
 *
 * @property int $id
 * @property string $insulation_grade
 * @property string $insulation_factor
 * @property int $energy_consumption_per_m2
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static Builder|KeyFigureInsulationFactor forInsulationFactor($factor)
 * @method static Builder|KeyFigureInsulationFactor newModelQuery()
 * @method static Builder|KeyFigureInsulationFactor newQuery()
 * @method static Builder|KeyFigureInsulationFactor query()
 * @method static Builder|KeyFigureInsulationFactor whereCreatedAt($value)
 * @method static Builder|KeyFigureInsulationFactor whereEnergyConsumptionPerM2($value)
 * @method static Builder|KeyFigureInsulationFactor whereId($value)
 * @method static Builder|KeyFigureInsulationFactor whereInsulationFactor($value)
 * @method static Builder|KeyFigureInsulationFactor whereInsulationGrade($value)
 * @method static Builder|KeyFigureInsulationFactor whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class KeyFigureInsulationFactor extends Model
{

    public function scopeForInsulationFactor(Builder $query, $factor)
    {
        $factor = number_format($factor, 2, '.', '');

        return $query->where('insulation_factor', '=', $factor);
    }
}
