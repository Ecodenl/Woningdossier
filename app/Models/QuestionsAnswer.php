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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionsAnswer allInputSources()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionsAnswer forBuilding(\App\Models\Building|int $building)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionsAnswer forInputSource(\App\Models\InputSource $inputSource)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionsAnswer forMe(?\App\Models\User $user = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionsAnswer forUser(\App\Models\User|int $user)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionsAnswer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionsAnswer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionsAnswer query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionsAnswer residentInput()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionsAnswer whereAnswer($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionsAnswer whereBuildingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionsAnswer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionsAnswer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionsAnswer whereInputSourceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionsAnswer whereQuestionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionsAnswer whereUpdatedAt($value)
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
