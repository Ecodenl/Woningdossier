<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
