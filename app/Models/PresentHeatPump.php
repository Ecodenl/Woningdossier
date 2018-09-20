<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\PresentHeatPump.
 *
 * @property int $id
 * @property string $name
 * @property int $calculate_value
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PresentHeatPump whereCalculateValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PresentHeatPump whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PresentHeatPump whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PresentHeatPump whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PresentHeatPump whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class PresentHeatPump extends Model
{
}
