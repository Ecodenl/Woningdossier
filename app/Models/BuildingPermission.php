<?php

namespace App\Models;

use App\Traits\GetValueTrait;
use Illuminate\Database\Eloquent\Model;

class BuildingPermission extends Model
{
    use GetValueTrait;

    protected $fillable = ['user_id', 'role_id', 'step_id', 'building_id', 'permissions'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'permissions' => 'array',
    ];

    /**
     * Return the building from the permission
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function building()
    {
        return $this->belongsTo('App\Models\Building');
    }

}
