<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\KeyFigureInsulationFactor
 *
 * @method static Builder|KeyFigureInsulationFactor forInsulationFactor($factor)
 * @method static Builder|KeyFigureInsulationFactor newModelQuery()
 * @method static Builder|KeyFigureInsulationFactor newQuery()
 * @method static Builder|KeyFigureInsulationFactor query()
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
