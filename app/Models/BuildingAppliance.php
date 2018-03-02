<?php

namespace App\Models;

use App\Models\Address;
use App\Models\Appliance;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\BuildingAppliance
 *
 * @property-read \App\Models\Address $address
 * @property-read \App\Models\Appliance $appliance
 * @mixin \Eloquent
 * @property int $id
 * @property int|null $address_id
 * @property int|null $appliance_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingAppliance whereAddressId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingAppliance whereApplianceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingAppliance whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingAppliance whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingAppliance whereUpdatedAt($value)
 */
class BuildingAppliance extends Model
{
    public function address(){
    	return $this->belongsTo(Address::class);
    }

    public function appliance(){
    	return $this->belongsTo(Appliance::class);
    }
}
