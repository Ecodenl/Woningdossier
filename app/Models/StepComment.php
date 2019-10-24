<?php

namespace App\Models;

use App\Traits\GetMyValuesTrait;
use App\Traits\GetValueTrait;
use Illuminate\Database\Eloquent\Model;

class StepComment extends Model
{
    use GetValueTrait, GetMyValuesTrait;

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
