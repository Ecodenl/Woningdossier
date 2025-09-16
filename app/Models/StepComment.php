<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\GetMyValuesTrait;
use App\Traits\GetValueTrait;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * App\Models\StepComment
 *
 * @property int $id
 * @property int $building_id
 * @property int $input_source_id
 * @property string|null $short
 * @property int $step_id
 * @property string|null $comment
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \App\Models\InputSource $inputSource
 * @property-read \App\Models\Step $step
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StepComment allInputSources()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StepComment forBuilding(\App\Models\Building|int $building)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StepComment forInputSource(\App\Models\InputSource $inputSource)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StepComment forMe(?\App\Models\User $user = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StepComment forUser(\App\Models\User|int $user)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StepComment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StepComment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StepComment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StepComment residentInput()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StepComment whereBuildingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StepComment whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StepComment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StepComment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StepComment whereInputSourceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StepComment whereShort($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StepComment whereStepId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StepComment whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class StepComment extends Model implements Auditable
{
    use GetValueTrait,
        GetMyValuesTrait,
        \App\Traits\Models\Auditable;

    protected $fillable = [
        'comment', 'input_source_id', 'building_id', 'short', 'step_id',
    ];

    /**
     * Return the step of a comment.
     */
    public function step(): BelongsTo
    {
        return $this->belongsTo(Step::class);
    }
}
