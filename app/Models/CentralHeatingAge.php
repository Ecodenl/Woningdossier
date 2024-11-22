<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\CentralHeatingAge
 *
 * @property int $id
 * @property string $name
 * @property int $calculate_value
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CentralHeatingAge newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CentralHeatingAge newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CentralHeatingAge query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CentralHeatingAge whereCalculateValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CentralHeatingAge whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CentralHeatingAge whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CentralHeatingAge whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CentralHeatingAge whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class CentralHeatingAge extends Model
{
}
