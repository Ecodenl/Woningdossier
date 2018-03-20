<?php

namespace App\Models;

use App\Models\Address;
use App\Models\Measure;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Device
 *
 * @property-read \App\Models\Address $address
 * @property-read \App\Models\DeviceType $deviceType
 * @property-read \App\Models\Measure $measure
 * @mixin \Eloquent
 * @property int $id
 * @property int|null $measure_id
 * @property int|null $address_id
 * @property int|null $device_type_id
 * @property string $name
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Device whereAddressId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Device whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Device whereDeviceTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Device whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Device whereMeasureId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Device whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Device whereUpdatedAt($value)
 * @property-read \App\Models\Building $building
 */
class Device extends Model
{
    public function measure(){
    	return $this->belongsTo(Measure::class);
    }

    public function building(){
    	return $this->belongsTo(Building::class);
    }

    public function deviceType(){
    	return $this->belongsTo(DeviceType::class);
    }
}
