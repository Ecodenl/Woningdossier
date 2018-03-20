<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\AddressUserUsage
 *
 * @property-read \App\Models\Address $address
 * @property-read \App\Models\User $user
 * @mixin \Eloquent
 * @property int $id
 * @property int|null $address_id
 * @property int|null $user_id
 * @property int|null $usage_percentage
 * @property string|null $start_date
 * @property string|null $end_date
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AddressUserUsage whereAddressId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AddressUserUsage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AddressUserUsage whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AddressUserUsage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AddressUserUsage whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AddressUserUsage whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AddressUserUsage whereUsagePercentage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AddressUserUsage whereUserId($value)
 * @property-read \App\Models\Building $building
 */
class BuildingUserUsage extends Model
{
    public function building(){
    	return $this->belongsTo(Building::class);
    }

    public function user(){
    	return $this->belongsTo(User::class);
    }

}