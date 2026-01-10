<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;

class BuildingSetting extends Model implements Auditable
{
    use \App\Traits\Models\Auditable;

    public $fillable = [
        'building_id', 'short', 'value',
    ];

    # Scopes
    #[Scope]
    protected function forShort(Builder $query, string $short): Builder
    {
        return $query->where('short', $short);
    }

    # Relations
    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }
}
