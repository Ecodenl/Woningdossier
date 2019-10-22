<?php

namespace App\Models;

use App\Helpers\HoomdossierSession;
use App\Helpers\TranslatableTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Question.
 *
 * @property int                                                                    $id
 * @property string                                                                 $name
 * @property string                                                                 $type
 * @property int                                                                    $order
 * @property bool                                                                   $required
 * @property array|null                                                             $validation
 * @property int                                                                    $questionnaire_id
 * @property \Illuminate\Support\Carbon|null                                        $created_at
 * @property \Illuminate\Support\Carbon|null                                        $updated_at
 * @property \Illuminate\Support\Carbon|null                                        $deleted_at
 * @property \Illuminate\Database\Eloquent\Collection|\App\Models\QuestionsAnswer[] $questionAnswers
 * @property \Illuminate\Database\Eloquent\Collection|\App\Models\QuestionOption[]  $questionOptions
 * @property \App\Models\Questionnaire                                              $questionnaire
 *
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Question newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Question newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Question onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Question query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Question translated($attribute, $name, $locale = 'nl')
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Question whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Question whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Question whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Question whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Question whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Question whereQuestionnaireId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Question whereRequired($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Question whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Question whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Question whereValidation($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Question withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Question withoutTrashed()
 * @mixin \Eloquent
 */
class Question extends Model
{
    use TranslatableTrait;
    use SoftDeletes;

    protected $fillable = [
        'name', 'type', 'order', 'required', 'questionnaire_id', 'validation',
    ];

    protected $dates = [
        'deleted_at',
    ];
    protected $casts = [
        'required' => 'bool',
        'validation' => 'array',
    ];

    /**
     * Check if a question is required.
     *
     * @return bool
     */
    public function isRequired(): bool
    {
        if (true == $this->required) {
            return true;
        }

        return false;
    }

    /**
     * Check if a question has validation.
     *
     * @return bool
     */
    public function hasValidation(): bool
    {
        if (is_array($this->validation) && ! empty($this->validation)) {
            return true;
        }

        return false;
    }

    /**
     * Check if a question has validation.
     *
     * @return bool
     */
    public function hasNoValidation(): bool
    {
        return ! $this->hasValidation();
    }

    /**
     * Return the options from a questions, a question will have options if its a radio, checkbox or dropdown etc.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function questionOptions()
    {
        return $this->hasMany(QuestionOption::class);
    }

    /**
     * Check if a question has a question option.
     *
     * @return bool
     */
    public function hasQuestionOptions()
    {
        if ($this->questionOptions()->first() instanceof QuestionOption) {
            return true;
        }

        return false;
    }

    /**
     * Return all the answers for a question.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function questionAnswers()
    {
        return $this->hasMany(QuestionsAnswer::class);
    }

    /**
     * Return the answer on a question for a building and input source.
     *
     * @return Model|\Illuminate\Database\Eloquent\Relations\HasMany|object|null
     */
    public function getAnswerForCurrentInputSource()
    {
        $currentAnswerForInputSource = $this->questionAnswers()
            ->where('building_id', HoomdossierSession::getBuilding())
            ->where('input_source_id', HoomdossierSession::getInputSource())->first();

        if ($currentAnswerForInputSource instanceof QuestionsAnswer) {
            return $currentAnswerForInputSource->answer;
        }

        return null;
    }

    /**
     * Get the questionnaire from a question.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function questionnaire()
    {
        return $this->belongsTo(Questionnaire::class);
    }
}
