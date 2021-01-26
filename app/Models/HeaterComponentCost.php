<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\HeaterComponentCost
 *
 * @property int $id
 * @property string $component
 * @property string $size
 * @property string $cost
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|HeaterComponentCost newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|HeaterComponentCost newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|HeaterComponentCost query()
 * @method static \Illuminate\Database\Eloquent\Builder|HeaterComponentCost whereComponent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HeaterComponentCost whereCost($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HeaterComponentCost whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HeaterComponentCost whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HeaterComponentCost whereSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HeaterComponentCost whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class HeaterComponentCost extends Model
{
}
