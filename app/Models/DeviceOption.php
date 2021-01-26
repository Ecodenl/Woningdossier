<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\DeviceOption.
 *
 * @property int                             $id
 * @property string                          $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|DeviceOption newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DeviceOption newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DeviceOption query()
 * @method static \Illuminate\Database\Eloquent\Builder|DeviceOption whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DeviceOption whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DeviceOption whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DeviceOption whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class DeviceOption extends Model
{
}
