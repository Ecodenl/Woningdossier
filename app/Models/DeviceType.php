<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\DeviceType.
 *
 * @property int $id
 * @property string $name
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DeviceType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DeviceType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DeviceType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DeviceType whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class DeviceType extends Model
{
}
