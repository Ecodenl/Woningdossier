<?php

namespace App\Models;

use App\Helpers\TranslatableTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\QuestionOption
 *
 * @property int $id
 * @property int $question_id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\QuestionOption newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\QuestionOption newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\QuestionOption query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\QuestionOption translated($attribute, $name, $locale = 'nl')
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\QuestionOption whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\QuestionOption whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\QuestionOption whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\QuestionOption whereQuestionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\QuestionOption whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class QuestionOption extends Model
{
    use TranslatableTrait;

    protected $fillable = [
        'question_id', 'name',
    ];
}
