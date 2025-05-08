<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingPermission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingPermission newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingPermission query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingPermission whereBuildingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingPermission whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingPermission whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingPermission whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingPermission whereUserId($value)
 * @mixin \Eloquent
 */
class BuildingPermission extends Model
{
    protected $fillable = ['user_id', 'building_id'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'permissions' => 'array',
        ];
    }

    /**
     * Return the building from the permission.
     */
    public function building(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Building::class);
    }

    public function buildingCoachStatus(): BelongsTo
    {
        return $this->belongsTo(\App\Models\BuildingCoachStatus::class, 'building_id', 'building_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
