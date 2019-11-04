<?php

namespace App\Models;

use App\Traits\GetMyValuesTrait;
use App\Traits\GetValueTrait;
use Illuminate\Database\Eloquent\Model;

class BuildingVentilation extends Model
{
    use GetMyValuesTrait, GetValueTrait;

    protected $casts = [
        'living_situation' => 'array',
        'usage'            => 'array',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function building()
    {
        return $this->belongsTo(Building::class);
    }
}
