<?php

namespace App\Models;

use App\Events\StepDataHasBeenChanged;
use App\Helpers\Hoomdossier;
use App\Helpers\StepHelper;
use App\Traits\GetMyValuesTrait;
use App\Traits\GetValueTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompletedSubStep extends Model
{

    use GetMyValuesTrait,
        GetValueTrait;

    protected $fillable = ['sub_step_id', 'building_id', 'input_source_id'];

    protected static function boot()
    {
        parent::boot();

        static::saved(function ($completedSubStep) {

        });
    }

    public function subStep(): BelongsTo
    {
        return $this->belongsTo(SubStep::class);
    }

    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }
}
