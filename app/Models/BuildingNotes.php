<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\BuildingNotes.
 *
 * @property int $id
 * @property string $note
 * @property int|null $coach_id
 * @property int|null $building_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \App\Models\Building|null $building
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingNotes newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingNotes newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingNotes query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingNotes whereBuildingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingNotes whereCoachId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingNotes whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingNotes whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingNotes whereNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingNotes whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class BuildingNotes extends Model
{
    protected $fillable = [
        'coach_id',
        'building_id',
        'note',
    ];

    public function building()
    {
        return $this->belongsTo(Building::class, 'building_id', 'id');
    }
}
