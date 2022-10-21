<?php

namespace App\Models;

use App\Traits\HasShortTrait;
use App\Traits\Models\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class ToolCalculationResult extends Model
{
    use HasTranslations, HasShortTrait;

    protected $translatable = [
        'name',
        'help_text'
    ];

    # Relations
    public function subSteppables(): MorphMany
    {
        return $this->morphMany(SubSteppable::class, 'sub_steppable');
    }

    public function subSteps(): BelongsToMany
    {
        return $this->morphToMany(SubStep::class, 'sub_steppable')
            ->using(SubSteppable::class)
            ->withPivot('order', 'size', 'conditions', 'tool_question_type_id');
    }
}
