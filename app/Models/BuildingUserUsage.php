<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\BuildingUserUsage
 *
 * @property int $id
 * @property int|null $building_id
 * @property int|null $user_id
 * @property int|null $usage_percentage
 * @property string|null $start_date
 * @property string|null $end_date
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Models\Building|null $building
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingUserUsage whereBuildingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingUserUsage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingUserUsage whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingUserUsage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingUserUsage whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingUserUsage whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingUserUsage whereUsagePercentage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingUserUsage whereUserId($value)
 * @mixin \Eloquent
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