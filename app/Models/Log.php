<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * App\Models\Log
 *
 * @property int $id
 * @property string|null $loggable_type
 * @property int|null $loggable_id
 * @property int|null $building_id
 * @property string $message
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Building|null $building
 * @property-read Model|\Eloquent|null $loggable
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Log forBuildingId($buildingId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Log newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Log newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Log query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Log whereBuildingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Log whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Log whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Log whereLoggableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Log whereLoggableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Log whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Log whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Log extends Model
{
    protected $fillable = [
        'loggable_type', 'loggable_id', 'building_id', 'message',
    ];

    /**
     * Scope a query to return all the logs for a given building id.
     *
     * @param $query
     * @param $buildingId
     *
     * @return mixed
     */
    #[Scope]
    protected function forBuildingId($query, $buildingId)
    {
        return $query->where('building_id', $buildingId);
    }

    /**
     * Return the model that did the action.
     */
    public function loggable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Return the building whom the action was performed on.
     */
    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class, 'building_id', 'id');
    }
}
