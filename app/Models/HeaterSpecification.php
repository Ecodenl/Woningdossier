<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\HeaterSpecification
 *
 * @property int $id
 * @property int $liters
 * @property int $savings
 * @property int $boiler
 * @property numeric $collector
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HeaterSpecification newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HeaterSpecification newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HeaterSpecification query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HeaterSpecification whereBoiler($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HeaterSpecification whereCollector($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HeaterSpecification whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HeaterSpecification whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HeaterSpecification whereLiters($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HeaterSpecification whereSavings($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HeaterSpecification whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class HeaterSpecification extends Model
{

    protected function casts(): array
    {
        return [
            'collector' => 'decimal:1',
        ];
    }
}
