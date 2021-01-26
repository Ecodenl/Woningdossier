<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\BuildingPermission
 *
 * @property int $id
 * @property int $user_id
 * @property int $building_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Building $building
 * @property-read \App\Models\BuildingCoachStatus $buildingCoachStatus
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingPermission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingPermission newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingPermission query()
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingPermission whereBuildingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingPermission whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingPermission whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingPermission whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingPermission whereUserId($value)
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
        return $this->belongsTo(\App\Models\Building::class);
    }

    public function buildingCoachStatus()
    {
        return $this->belongsTo(\App\Models\BuildingCoachStatus::class, 'building_id', 'building_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
