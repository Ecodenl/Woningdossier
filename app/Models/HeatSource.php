<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\HeatSource
 *
 * @property int $id
 * @property string $name
 * @property int $calculate_value
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HeatSource newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HeatSource newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HeatSource query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HeatSource whereCalculateValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HeatSource whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HeatSource whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HeatSource whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HeatSource whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class HeatSource extends Model
{
}
