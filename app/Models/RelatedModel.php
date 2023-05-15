<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class RelatedModel extends Model
{
    use HasFactory;

    protected $fillable = [
        'from_model_type',
        'from_model_id',
        'target_model_type',
        'target_model_id',
    ];

    # Relations
    public function resolvable(): MorphTo
    {
        return $this->morphTo('from_model');
    }

    public function targetable(): MorphTo
    {
        return $this->morphTo('target_model');
    }
}
