<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompletedSubStep extends Model
{
    protected $fillable = ['sub_step_id', 'building_id', 'input_source_id'];

    public function subStep(): BelongsTo
    {
        return $this->belongsTo(SubStep::class);
    }
}
