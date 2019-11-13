<?php

namespace App\Models;

use App\Traits\GetMyValuesTrait;
use App\Traits\GetValueTrait;
use Illuminate\Database\Eloquent\Model;

class BuildingVentilation extends Model
{
    use GetMyValuesTrait, GetValueTrait;

    protected $casts = [
        'how'              => 'array',
        'living_situation' => 'array',
        'usage'            => 'array',
    ];

    protected $fillable = [
        'building_id', 'input_source_id', 'how', 'living_situation', 'usage',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function building()
    {
        return $this->belongsTo(Building::class);
    }
}
