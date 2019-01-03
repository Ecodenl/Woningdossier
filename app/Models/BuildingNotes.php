<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
