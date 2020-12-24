<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

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
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingCoachStatus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingCoachStatus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingCoachStatus query()
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingCoachStatus whereBuildingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingCoachStatus whereCoachId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingCoachStatus whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingCoachStatus whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingCoachStatus whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingCoachStatus whereUpdatedAt($value)
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
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function building()
    {
        return $this->belongsTo(Building::class);
    }

    /**
     * Get the user / coach from the status this does NOT return the owner from the building.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'coach_id', 'id');
    }

    /**
     * Get the coach from the status.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function coach()
    {
        return $this->belongsTo(User::class, 'coach_id', 'id');
    }

    /**
     * Get the current status for a given building id, can return the translation or the status key
     * will return the translation by default.
     */
    public static function getCurrentStatusForBuildingId(int $buildingId, bool $returnTranslation = true): string
    {
        \Illuminate\Support\Facades\Log::debug(__METHOD__.' is still being used, remove it as soon as possible.');
        // get the building, even if its deleted.
        $building = Building::withTrashed()->find($buildingId);

        if ($building instanceof Building) {
            return $building->getMostRecentBuildingStatus()->status->name;
        }

        return '';
    }
}
