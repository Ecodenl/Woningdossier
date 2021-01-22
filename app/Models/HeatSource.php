<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\HeatSource.
 *
 * @property int                             $id
 * @property string                          $name
 * @property int                             $calculate_value
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|HeatSource newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|HeatSource newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|HeatSource query()
 * @method static \Illuminate\Database\Eloquent\Builder|HeatSource whereCalculateValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HeatSource whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HeatSource whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HeatSource whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HeatSource whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class HeatSource extends Model
{
}
