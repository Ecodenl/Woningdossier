<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\CentralHeatingAge.
 *
 * @property int                             $id
 * @property string                          $name
 * @property int                             $calculate_value
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|CentralHeatingAge newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CentralHeatingAge newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CentralHeatingAge query()
 * @method static \Illuminate\Database\Eloquent\Builder|CentralHeatingAge whereCalculateValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CentralHeatingAge whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CentralHeatingAge whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CentralHeatingAge whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CentralHeatingAge whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class CentralHeatingAge extends Model
{
}
