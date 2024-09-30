<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\GetMyValuesTrait;
use App\Traits\GetValueTrait;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\QuestionsAnswer
 *
 * @property int $id
 * @property int $question_id
 * @property int|null $building_id
 * @property int $input_source_id
 * @property string $answer
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\InputSource $inputSource
 * @property-read \App\Models\Question $question
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionsAnswer allInputSources()
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionsAnswer forBuilding($building)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionsAnswer forInputSource(\App\Models\InputSource $inputSource)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionsAnswer forMe(?\App\Models\User $user = null)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionsAnswer forUser($user)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionsAnswer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionsAnswer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionsAnswer query()
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionsAnswer residentInput()
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionsAnswer whereAnswer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionsAnswer whereBuildingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionsAnswer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionsAnswer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionsAnswer whereInputSourceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionsAnswer whereQuestionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionsAnswer whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class QuestionsAnswer extends Model
{
    use GetValueTrait;
    use GetMyValuesTrait;
    

    protected $fillable = [
        'question_id', 'building_id', 'input_source_id', 'answer',
    ];

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    public function inputSource(): BelongsTo
    {
        return $this->belongsTo(InputSource::class);
    }
}
