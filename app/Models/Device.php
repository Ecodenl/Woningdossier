<?php

namespace App\Models;

use App\Traits\GetValueTrait;
use App\Traits\ToolSettingTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Device.
 *
 * @property int                             $id
 * @property int|null                        $measure_id
 * @property int|null                        $building_id
 * @property int|null                        $input_source_id
 * @property int|null                        $device_type_id
 * @property string                          $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \App\Models\Building|null       $building
 * @property \App\Models\DeviceType|null     $deviceType
 * @property \App\Models\Measure|null        $measure
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Device newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Device newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Device query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Device whereBuildingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Device whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Device whereDeviceTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Device whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Device whereInputSourceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Device whereMeasureId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Device whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Device whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Device extends Model
{
    use GetValueTrait;
    use ToolSettingTrait;

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
