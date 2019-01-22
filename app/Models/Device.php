<?php

namespace App\Models;

use App\Traits\GetValueTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Device.
 *
 * @property int $id
 * @property int|null $measure_id
 * @property int|null $building_id
 * @property int|null $device_type_id
 * @property string $name
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property \App\Models\Building|null $building
 * @property \App\Models\DeviceType|null $deviceType
 * @property \App\Models\Measure|null $measure
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Device whereBuildingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Device whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Device whereDeviceTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Device whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Device whereMeasureId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Device whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Device whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Device extends Model
{
    use GetValueTrait, ToolSettingTrait;

    public function measure()
    {
        return $this->belongsTo(Measure::class);
    }

    public function building()
    {
        return $this->belongsTo(Building::class);
    }

    public function deviceType()
    {
        return $this->belongsTo(DeviceType::class);
    }
}
