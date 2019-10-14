<?php

namespace App\Models;

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
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\QuestionsAnswer forInputSource(\App\Models\InputSource $inputSource)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\QuestionsAnswer forMe(\App\Models\User $user = null)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\QuestionsAnswer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\QuestionsAnswer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\QuestionsAnswer query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\QuestionsAnswer residentInput()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\QuestionsAnswer whereAnswer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\QuestionsAnswer whereBuildingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\QuestionsAnswer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\QuestionsAnswer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\QuestionsAnswer whereInputSourceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\QuestionsAnswer whereQuestionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\QuestionsAnswer whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class QuestionsAnswer extends Model
{
    use GetValueTrait, GetMyValuesTrait;

    protected $fillable = [
        'question_id', 'building_id', 'input_source_id', 'answer',
    ];

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function inputSource()
    {
        return $this->belongsTo(InputSource::class);
    }
}
