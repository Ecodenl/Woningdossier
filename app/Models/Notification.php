<?php

namespace App\Models;

use App\Traits\GetMyValuesTrait;
use App\Traits\GetValueTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Input;

class Notification extends Model
{
    use GetMyValuesTrait;
    use GetValueTrait;

    protected $fillable = [
        'type',
        'building_id',
        'input_source_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function scopeActive(Builder $query)
    {
        return $query->where('is_active', true);
    }

    public function scopeActiveNotifications(Builder $query, Building $building, InputSource $inputSource)
    {
        return $query
            ->active()
            ->forBuilding($building)
            ->forInputSource($inputSource);
    }

    public static function setActive(Building $building, InputSource $inputSource, bool $active = false)
    {
        Notification::allInputSources()->updateOrCreate([
            'input_source_id' => $inputSource->id,
            'type' => 'recalculate',
            // the building owner is always passed to the job.
            'building_id' => $building->id,
        ], ['is_active' => $active]);
    }
}
