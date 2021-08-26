<?php

namespace App\Models;

use App\Traits\GetMyValuesTrait;
use App\Traits\GetValueTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompletedSubStep extends Model
{

    use GetMyValuesTrait;
    use GetValueTrait;

    protected $fillable = ['sub_step_id', 'building_id', 'input_source_id'];

    public function subStep(): BelongsTo
    {
        return $this->belongsTo(SubStep::class);
    }
}
