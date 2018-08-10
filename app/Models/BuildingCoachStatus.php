<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BuildingCoachStatus extends Model
{
    protected $fillable = [
        'coach_id', 'status', 'building_id'
    ];

    /**
     * Get the building from the status
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function building()
    {
        return $this->belongsTo('App\Models\Building');
    }

    /**
     * Get the coach from the status
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function coach()
    {
        return $this->belongsTo('App\Models\User');
    }
}
