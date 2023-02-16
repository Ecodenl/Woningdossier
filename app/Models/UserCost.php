<?php

namespace App\Models;

use App\Traits\GetMyValuesTrait;
use App\Traits\GetValueTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use OwenIt\Auditing\Contracts\Auditable;

class UserCost extends Model implements Auditable
{
    use GetValueTrait,
        GetMyValuesTrait,
        \App\Traits\Models\Auditable;

    public $fillable = [
        'user_id', 'input_source_id', 'advisable_type', 'advisable_id', 'own_total', 'subsidy_total',
    ];

    # Scopes
    public function scopeForAdvisable(Builder $query, Model $advisable): Builder
    {
        return $query->where('advisable_type', get_class($advisable))
            ->where('advisable_id', $advisable->id);
    }

    # Relations
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function advisable(): MorphTo
    {
        return $this->morphTo();
    }
}
