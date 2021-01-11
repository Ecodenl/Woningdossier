<?php

namespace App\Models;

use App\Traits\GetMyValuesTrait;
use App\Traits\GetValueTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\StepComment
 *
 * @property int $id
 * @property int $building_id
 * @property int $input_source_id
 * @property string|null $short
 * @property int $step_id
 * @property string $comment
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\InputSource $inputSource
 * @property-read \App\Models\Step $step
 * @method static \Illuminate\Database\Eloquent\Builder|StepComment allInputSources()
 * @method static \Illuminate\Database\Eloquent\Builder|StepComment forBuilding(\App\Models\Building $building)
 * @method static \Illuminate\Database\Eloquent\Builder|StepComment forInputSource(\App\Models\InputSource $inputSource)
 * @method static \Illuminate\Database\Eloquent\Builder|StepComment forMe(\App\Models\User $user = null)
 * @method static \Illuminate\Database\Eloquent\Builder|StepComment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StepComment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StepComment query()
 * @method static \Illuminate\Database\Eloquent\Builder|StepComment residentInput()
 * @method static \Illuminate\Database\Eloquent\Builder|StepComment whereBuildingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StepComment whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StepComment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StepComment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StepComment whereInputSourceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StepComment whereShort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StepComment whereStepId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StepComment whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class StepComment extends Model
{
    use GetValueTrait;
    use GetMyValuesTrait;

    protected $fillable = [
        'comment', 'input_source_id', 'building_id', 'short', 'step_id',
    ];

    /**
     * Return the step of a comment.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function step()
    {
        return $this->belongsTo(Step::class);
    }
}
