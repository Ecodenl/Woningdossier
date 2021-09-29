<?php

namespace App\Models;

use App\Scopes\VisibleScope;
use App\Traits\Models\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CooperationMeasureApplication extends Model
{
    use HasTranslations, SoftDeletes;

    protected $translatable = ['name', 'info'];

    protected $fillable = [
        'name', 'info', 'costs', 'savings_money', 'extra', 'cooperation_id',
    ];

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
