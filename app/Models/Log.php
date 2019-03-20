<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    protected $fillable = [
        'user_id', 'building_id', 'message', 'for_user_id'
    ];

    /**
     * Scope a query to return all the logs for a given building id
     *
     * @param $query
     * @param $buildingId
     * @return mixed
     */
    public function scopeForBuildingId($query, $buildingId)
    {
        return $query->where('building_id', $buildingId);
    }

    /**
     * Return the user that did the action
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Return the user whom the action was performed on
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function forUser()
    {
        return $this->belongsTo(User::class, 'for_user_id', 'id');
    }

    /**
     * Return the building whom the action was performed on
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function building()
    {
        return $this->belongsTo(Building::class, 'building_id', 'id');
    }
}
