<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphPivot;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class SubSteppable extends MorphPivot
{
    protected $table = 'sub_steppables';

    protected $casts = [
        'conditions' => 'array',
    ];

    # Relations
    public function subSteppable(): MorphTo
    {
        return $this->morphTo();
    }

    public function toolQuestionType(): BelongsTo
    {
        return $this->belongsTo(ToolQuestionType::class);
    }

    public function isToolQuestion(): bool
    {
        return $this->sub_steppable_type == ToolQuestion::class;
    }
}
