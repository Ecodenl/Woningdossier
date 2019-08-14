<?php

namespace App\Models;

use App\Traits\GetMyValuesTrait;
use App\Traits\GetValueTrait;
use App\Traits\ToolSettingTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\BuildingUserUsage
 *
 * @property-read \App\Models\Building $building
 * @property-read \App\Models\InputSource $inputSource
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingUserUsage forMe()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingUserUsage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingUserUsage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingUserUsage query()
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
