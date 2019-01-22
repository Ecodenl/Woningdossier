<?php

namespace App\Models;

use App\Traits\GetMyValuesTrait;
use App\Traits\GetValueTrait;
use App\Traits\ToolSettingTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\BuildingUserUsage.
 *
 * @property int $id
 * @property int|null $building_id
 * @property int|null $input_source_id
 * @property int|null $user_id
 * @property int|null $usage_percentage
 * @property string|null $start_date
 * @property string|null $end_date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \App\Models\Building|null $building
 * @property \App\Models\InputSource|null $inputSource
 * @property \App\Models\User|null $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingUserUsage forMe()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingUserUsage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingUserUsage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingUserUsage query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingUserUsage whereBuildingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingUserUsage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingUserUsage whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingUserUsage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingUserUsage whereInputSourceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingUserUsage whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingUserUsage whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingUserUsage whereUsagePercentage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingUserUsage whereUserId($value)
 * @mixin \Eloquent
 */
class BuildingUserUsage extends Model
{
    use GetValueTrait, GetMyValuesTrait, ToolSettingTrait;

    public function building()
    {
        return $this->belongsTo(Building::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
