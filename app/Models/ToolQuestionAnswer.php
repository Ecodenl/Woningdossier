<?php

namespace App\Models;

use App\Traits\GetMyValuesTrait;
use App\Traits\GetValueTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
 * @property-read \App\Models\Building $building
 * @property-read \App\Models\InputSource $inputSource
 * @property-read \App\Models\ToolQuestion $toolQuestion
 * @property-read \App\Models\ToolQuestionCustomValue|null $toolQuestionCustomValue
 * @method static \Illuminate\Database\Eloquent\Builder|ToolQuestionAnswer allInputSources()
 * @method static \Illuminate\Database\Eloquent\Builder|ToolQuestionAnswer forBuilding($building)
 * @method static \Illuminate\Database\Eloquent\Builder|ToolQuestionAnswer forInputSource(\App\Models\InputSource $inputSource)
 * @method static \Illuminate\Database\Eloquent\Builder|ToolQuestionAnswer forMe(?\App\Models\User $user = null)
 * @method static \Illuminate\Database\Eloquent\Builder|ToolQuestionAnswer forUser($user)
 * @method static \Illuminate\Database\Eloquent\Builder|ToolQuestionAnswer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ToolQuestionAnswer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ToolQuestionAnswer query()
 * @method static \Illuminate\Database\Eloquent\Builder|ToolQuestionAnswer residentInput()
 * @method static \Illuminate\Database\Eloquent\Builder|ToolQuestionAnswer whereAnswer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ToolQuestionAnswer whereBuildingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ToolQuestionAnswer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ToolQuestionAnswer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ToolQuestionAnswer whereInputSourceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ToolQuestionAnswer whereToolQuestionCustomValueId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ToolQuestionAnswer whereToolQuestionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ToolQuestionAnswer whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ToolQuestionAnswer extends Model
{
    use GetValueTrait, GetMyValuesTrait;

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
