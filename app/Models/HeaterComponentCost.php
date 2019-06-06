<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\HeaterComponentCost
 *
 * @property int $id
 * @property string $component
 * @property float $size
 * @property float $cost
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\HeaterComponentCost newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\HeaterComponentCost newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\HeaterComponentCost query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\HeaterComponentCost whereComponent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\HeaterComponentCost whereCost($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\HeaterComponentCost whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\HeaterComponentCost whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\HeaterComponentCost whereSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\HeaterComponentCost whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class HeaterComponentCost extends Model
{
}
