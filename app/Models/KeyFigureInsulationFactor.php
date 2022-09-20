<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class KeyFigureInsulationFactor extends Model
{

    public function scopeForInsulationFactor(Builder $query, $factor)
    {
        $factor = number_format($factor, 2, '', '.');

        return $query->where('insulation_factor', '=', $factor);
    }
}
