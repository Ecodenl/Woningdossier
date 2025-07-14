<?php

namespace App\Models;

use App\Observers\ToolQuestionAnswerObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use App\Traits\GetMyValuesTrait;
use App\Traits\GetValueTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * App\Models\ToolQuestionAnswer
 *
 * @property int $id
 * @property int $building_id
 * @property int $input_source_id
 * @property int $tool_question_id
 * @property int|null $tool_question_custom_value_id
 * @property string $answer
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \App\Models\Building $building
 * @property-read \App\Models\InputSource $inputSource
 * @property-read \App\Models\ToolQuestion $toolQuestion
 * @property-read \App\Models\ToolQuestionCustomValue|null $toolQuestionCustomValue
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ToolQuestionAnswer allInputSources()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ToolQuestionAnswer forBuilding(\App\Models\Building|int $building)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ToolQuestionAnswer forInputSource(\App\Models\InputSource $inputSource)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ToolQuestionAnswer forMe(?\App\Models\User $user = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ToolQuestionAnswer forUser(\App\Models\User|int $user)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ToolQuestionAnswer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ToolQuestionAnswer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ToolQuestionAnswer query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ToolQuestionAnswer residentInput()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ToolQuestionAnswer whereAnswer($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ToolQuestionAnswer whereBuildingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ToolQuestionAnswer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ToolQuestionAnswer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ToolQuestionAnswer whereInputSourceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ToolQuestionAnswer whereToolQuestionCustomValueId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ToolQuestionAnswer whereToolQuestionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ToolQuestionAnswer whereUpdatedAt($value)
 * @mixin \Eloquent
 */
#[ObservedBy([ToolQuestionAnswerObserver::class])]
class ToolQuestionAnswer extends Model implements Auditable
{
    use GetValueTrait,
        GetMyValuesTrait,
        \App\Traits\Models\Auditable;

    protected $fillable = [
        'building_id', 'input_source_id', 'tool_question_id', 'tool_question_custom_value_id', 'answer',
    ];

    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }

    public function toolQuestion(): BelongsTo
    {
        return $this->belongsTo(ToolQuestion::class);
    }

    public function toolQuestionCustomValue(): BelongsTo
    {
        return $this->belongsTo(ToolQuestionCustomValue::class);
    }
}
