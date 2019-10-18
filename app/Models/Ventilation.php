<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Ventilation.
 *
 * @property int                             $id
 * @property string                          $name
 * @property int                             $calculate_value
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Ventilation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Ventilation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Ventilation query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Ventilation whereCalculateValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Ventilation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Ventilation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Ventilation whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Ventilation whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Ventilation extends Model
{
}
