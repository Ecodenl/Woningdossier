<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\BuildingPermission.
 *
 * @property int                             $id
 * @property int                             $user_id
 * @property int                             $building_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \App\Models\Building            $building
 * @property \App\Models\BuildingCoachStatus $buildingCoachStatus
 * @property \App\Models\User                $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingPermission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingPermission newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingPermission query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingPermission whereBuildingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingPermission whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingPermission whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingPermission whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingPermission whereUserId($value)
 * @mixin \Eloquent
 */
class BuildingPermission extends Model
{
    protected $fillable = ['user_id', 'building_id'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'permissions' => 'array',
    ];

    /**
     * Return the building from the permission.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function building()
    {
        return $this->belongsTo('App\Models\Building');
    }

    public function buildingCoachStatus()
    {
        return $this->belongsTo('App\Models\BuildingCoachStatus', 'building_id', 'building_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
