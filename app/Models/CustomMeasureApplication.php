<?php

namespace App\Models;

use App\Traits\GetMyValuesTrait;
use App\Traits\GetValueTrait;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class CustomMeasureApplication extends Model
{
    use HasTranslations, GetMyValuesTrait, GetValueTrait;

    public $translatable = ['name'];

    protected $fillable = ['building_id', 'input_source_id', 'name', 'hash'];

    protected $casts = [
        'extra' => 'array'
    ];

    public function userActionPlanAdvices()
    {
        return $this->morphMany(UserActionPlanAdvice::class, 'user_action_plan_advisable');
    }
}
