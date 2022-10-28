<?php

namespace App\Models;

use App\Traits\GetMyValuesTrait;
use App\Traits\GetValueTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * App\Models\CompletedStep
 *
 * @property int $id
 * @property int|null $input_source_id
 * @property int $step_id
 * @property int|null $building_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Models\Audit[] $audits
 * @property-read int|null $audits_count
 * @property-read \App\Models\Building|null $building
 * @property-read \App\Models\InputSource|null $inputSource
 * @property-read \App\Models\Step $step
 * @method static \Illuminate\Database\Eloquent\Builder|CompletedStep allInputSources()
 * @method static \Illuminate\Database\Eloquent\Builder|CompletedStep forBuilding($building)
 * @method static \Illuminate\Database\Eloquent\Builder|CompletedStep forInputSource(\App\Models\InputSource $inputSource)
 * @method static \Illuminate\Database\Eloquent\Builder|CompletedStep forMe(?\App\Models\User $user = null)
 * @method static \Illuminate\Database\Eloquent\Builder|CompletedStep forUser($user)
 * @method static \Illuminate\Database\Eloquent\Builder|CompletedStep newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CompletedStep newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CompletedStep query()
 * @method static \Illuminate\Database\Eloquent\Builder|CompletedStep residentInput()
 * @method static \Illuminate\Database\Eloquent\Builder|CompletedStep whereBuildingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompletedStep whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompletedStep whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompletedStep whereInputSourceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompletedStep whereStepId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompletedStep whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class CompletedStep extends Model implements Auditable
{
    use GetMyValuesTrait,
        GetValueTrait,
        \App\Traits\Models\Auditable;

    public $fillable = [
        'user_id', 'step_id', 'building_id', 'input_source_id',
    ];

    # Relations
    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }

    public function inputSource(): BelongsTo
    {
        return $this->belongsTo(InputSource::class);
    }

    public function step(): BelongsTo
    {
        return $this->belongsTo(Step::class);
    }
}
