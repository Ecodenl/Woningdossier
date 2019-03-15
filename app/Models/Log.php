<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    protected $fillable = [
        'user_id', 'building_id', 'message', 'about_user_id'
    ];

    public function scopeForBuildingId($query, $buildingId)
    {
        return $query->where('building_id', $buildingId);
    }
}
