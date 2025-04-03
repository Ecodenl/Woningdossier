<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\HeaterComponentCost
 *
 * @property int $id
 * @property string $component
 * @property numeric $size
 * @property numeric $cost
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HeaterComponentCost newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HeaterComponentCost newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HeaterComponentCost query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HeaterComponentCost whereComponent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HeaterComponentCost whereCost($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HeaterComponentCost whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HeaterComponentCost whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HeaterComponentCost whereSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HeaterComponentCost whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class HeaterComponentCost extends Model
{
    protected $casts = [
        'size' => 'decimal:2',
        'cost' => 'decimal:2',
    ];
}
