<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Log
 *
 * @property int $id
 * @property int|null $user_id
 * @property int|null $building_id
 * @property int|null $for_user_id
 * @property string $message
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Building|null $building
 * @property-read \App\Models\User|null $forUser
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Log forBuildingId($buildingId)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Log newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Log newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Log query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Log whereBuildingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Log whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Log whereForUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Log whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Log whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Log whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Log whereUserId($value)
 * @mixin \Eloquent
 */
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
