<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\BuildingCoachStatus
 *
 * @property int $id
 * @property int|null $coach_id
 * @property int|null $building_id
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Building|null $building
 * @property-read \App\Models\User|null $coach
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingCoachStatus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingCoachStatus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingCoachStatus query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingCoachStatus whereBuildingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingCoachStatus whereCoachId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingCoachStatus whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingCoachStatus whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingCoachStatus whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingCoachStatus whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class BuildingCoachStatus extends Model
{
    protected $table = 'building_coach_statuses';

    protected $fillable = [
        'coach_id', 'status', 'building_id',
    ];

    // status will be set when a coach is added to a building
    const STATUS_ADDED = 'added';

    // when a user is removed from a building
    const STATUS_REMOVED = 'removed';

    /**
     * Get the building from the status.
     */
    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }

    /**
     * @deprecated use coach()!
     * Get the user / coach from the status this does NOT return the owner from the building.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'coach_id', 'id');
    }

    /**
     * Get the coach from the status.
     */
    public function coach(): BelongsTo
    {
        return $this->belongsTo(User::class, 'coach_id', 'id');
    }
}
