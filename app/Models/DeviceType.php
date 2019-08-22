<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\DeviceType.
 *
 * @property int                             $id
 * @property string                          $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DeviceType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DeviceType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DeviceType query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DeviceType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DeviceType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DeviceType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DeviceType whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class DeviceType extends Model
{
}
