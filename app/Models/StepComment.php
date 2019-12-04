<?php

namespace App\Models;

use App\Traits\GetMyValuesTrait;
use App\Traits\GetValueTrait;
use Illuminate\Database\Eloquent\Model;

class StepComment extends Model
{
    use GetValueTrait, GetMyValuesTrait;

    protected $fillable = [
        'comment', 'input_source_id', 'building_id', 'short', 'step_id'
    ];

    /**
     * Return the step of a comment
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function step()
    {
        return $this->belongsTo(Step::class);
    }
}
