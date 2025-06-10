<?php

namespace App\Models;

use App\Observers\CompletedSubStepObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use App\Traits\GetMyValuesTrait;
use App\Traits\GetValueTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * App\Models\CompletedSubStep
 *
 * @property int $id
 * @property int $sub_step_id
 * @property int $building_id
 * @property int $input_source_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \App\Models\Building $building
 * @property-read \App\Models\InputSource $inputSource
 * @property-read \App\Models\SubStep $subStep
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompletedSubStep allInputSources()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompletedSubStep forBuilding(\App\Models\Building|int $building)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompletedSubStep forInputSource(\App\Models\InputSource $inputSource)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompletedSubStep forMe(?\App\Models\User $user = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompletedSubStep forUser(\App\Models\User|int $user)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompletedSubStep newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompletedSubStep newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompletedSubStep query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompletedSubStep residentInput()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompletedSubStep whereBuildingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompletedSubStep whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompletedSubStep whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompletedSubStep whereInputSourceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompletedSubStep whereSubStepId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompletedSubStep whereUpdatedAt($value)
 * @mixin \Eloquent
 */
#[ObservedBy([CompletedSubStepObserver::class])]
class CompletedSubStep extends Model implements Auditable
{
    use GetMyValuesTrait,
        GetValueTrait,
        \App\Traits\Models\Auditable;

    protected $fillable = ['sub_step_id', 'building_id', 'input_source_id'];

    # Relations
    public function inputSource(): BelongsTo
    {
        return $this->belongsTo(InputSource::class);
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
