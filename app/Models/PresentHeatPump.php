<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\PresentHeatPump
 *
 * @property int $id
 * @property string $name
 * @property int $calculate_value
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PresentHeatPump newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PresentHeatPump newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PresentHeatPump query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PresentHeatPump whereCalculateValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PresentHeatPump whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PresentHeatPump whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PresentHeatPump whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PresentHeatPump whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class PresentHeatPump extends Model
{
}
