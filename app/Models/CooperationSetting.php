<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Plank\Mediable\Mediable;

class CooperationSetting extends Model
{
    use Mediable;

    public $fillable = [
        'cooperation_id', 'short', 'value',
    ];

    # Model methods
    //

    # Attributes
    //

    # Scopes
    public function scopeForShort(Builder $query, string $short): Builder
    {
        return $query->where('short', $short);
    }

    # Relations
    public function cooperation(): BelongsTo
    {
        return $this->belongsTo(Cooperation::class);
    }
}
