<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CooperationPresetContent extends Model
{
    protected $fillable = [
        'cooperation_preset_id', 'content',
    ];

    protected $casts = [
        'content' => 'array',
    ];

    public function cooperationPreset(): BelongsTo
    {
        return $this->belongsTo(CooperationPreset::class);
    }
}