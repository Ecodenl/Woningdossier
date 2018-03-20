<?php

namespace App\Models;

use App\Models\Address;
use App\Models\Appliance;
use Illuminate\Database\Eloquent\Model;


/**
 * App\Models\BuildingAppliance
 *
 * @property int $id
 * @property int|null $address_id
 * @property int|null $appliance_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Models\Appliance|null $appliance
 * @property-read \App\Models\Building $building
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingAppliance whereAddressId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingAppliance whereApplianceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingAppliance whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingAppliance whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingAppliance whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class BuildingAppliance extends Model
{
    public function building(){
    	return $this->belongsTo(Building::class);
    }

    public function appliance(){
    	return $this->belongsTo(Appliance::class);
    }
}
