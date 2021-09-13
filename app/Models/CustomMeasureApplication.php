<?php

namespace App\Models;

use App\Scopes\VisibleScope;
use App\Traits\GetMyValuesTrait;
use App\Traits\GetValueTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class CustomMeasureApplication extends Model
{
    use HasTranslations,
        GetMyValuesTrait,
        GetValueTrait;

    public $translatable = [
        'name', 'info',
    ];

    protected $fillable = [
        'building_id', 'input_source_id', 'name', 'hash', 'info',
    ];

    protected $casts = [
        'extra' => 'array',
    ];

    public function userActionPlanAdvices()
    {
        // We need to retrieve this without the visible tag
        // The visible tag defines whether it should be shown on my plan or not, but for other locations
        // (e.g. the question that adds them) it just defines if it's checked or not
        return $this->morphMany(
            UserActionPlanAdvice::class,
            'user_action_plan_advisable'
        )->withoutGlobalScope(VisibleScope::class);
    }

    public function getSibling(InputSource $inputSource)
    {
        return static::allInputSources()->where('hash', '=', $this->hash)->forInputSource(
            $inputSource
        )->first();
    }
}
