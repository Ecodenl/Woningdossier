<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CooperationPreset extends Model
{
    protected $fillable = [
        'title', 'short',
    ];

    public function cooperationPresetContents(): HasMany
    {
        return $this->hasMany(CooperationPresetContent::class);
    }

    public function getRouteKeyName()
    {
        return 'short';
    }
}