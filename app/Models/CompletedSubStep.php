<?php

namespace App\Models;

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
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Models\Audit[] $audits
 * @property-read int|null $audits_count
 * @property-read \App\Models\Building $building
 * @property-read \App\Models\InputSource $inputSource
 * @property-read \App\Models\SubStep $subStep
 * @method static \Illuminate\Database\Eloquent\Builder|CompletedSubStep allInputSources()
 * @method static \Illuminate\Database\Eloquent\Builder|CompletedSubStep forBuilding($building)
 * @method static \Illuminate\Database\Eloquent\Builder|CompletedSubStep forInputSource(\App\Models\InputSource $inputSource)
 * @method static \Illuminate\Database\Eloquent\Builder|CompletedSubStep forMe(?\App\Models\User $user = null)
 * @method static \Illuminate\Database\Eloquent\Builder|CompletedSubStep forUser($user)
 * @method static \Illuminate\Database\Eloquent\Builder|CompletedSubStep newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CompletedSubStep newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CompletedSubStep query()
 * @method static \Illuminate\Database\Eloquent\Builder|CompletedSubStep residentInput()
 * @method static \Illuminate\Database\Eloquent\Builder|CompletedSubStep whereBuildingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompletedSubStep whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompletedSubStep whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompletedSubStep whereInputSourceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompletedSubStep whereSubStepId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompletedSubStep whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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
