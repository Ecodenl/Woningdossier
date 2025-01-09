<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\BuildingNotes
 *
 * @property int $id
 * @property string $note
 * @property int|null $coach_id
 * @property int|null $building_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Building|null $building
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingNotes newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingNotes newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingNotes query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingNotes whereBuildingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingNotes whereCoachId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingNotes whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingNotes whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingNotes whereNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingNotes whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class BuildingNotes extends Model
{
    protected $fillable = [
        'coach_id',
        'building_id',
        'note',
    ];

    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class, 'building_id', 'id');
    }
}
