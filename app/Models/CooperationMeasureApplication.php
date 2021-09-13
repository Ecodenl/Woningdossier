<?php

namespace App\Models;

use App\Scopes\VisibleScope;
use App\Traits\Models\HasTranslations;
use Illuminate\Database\Eloquent\Model;

class CooperationMeasureApplication extends Model
{
    use HasTranslations;

    protected $translatable = ['name', 'info'];

    protected $casts = [
        'costs' => 'array',
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

    public function cooperation()
    {
        return $this->belongsTo(Cooperation::class);
    }
}
