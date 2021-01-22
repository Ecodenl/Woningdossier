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
 * @property int|null                                                               $question_answers_count
 * @property \Illuminate\Database\Eloquent\Collection|\App\Models\QuestionOption[]  $questionOptions
 * @property int|null                                                               $question_options_count
 * @property \App\Models\Questionnaire                                              $questionnaire
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Question newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Question newQuery()
 * @method static \Illuminate\Database\Query\Builder|Question onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Question query()
 * @method static \Illuminate\Database\Eloquent\Builder|Question translated($attribute, $name, $locale = 'nl')
 * @method static \Illuminate\Database\Eloquent\Builder|Question whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Question whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Question whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Question whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Question whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Question whereQuestionnaireId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Question whereRequired($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Question whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Question whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Question whereValidation($value)
 * @method static \Illuminate\Database\Query\Builder|Question withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Question withoutTrashed()
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
        
    ];
    protected $casts = [
        'required' => 'bool',
        'validation' => 'array',
    ];

    /**
     * Check if a question is required.
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

    public function getQuestionAnswersForInputSource(InputSource $inputSource)
    {
        return $this
            ->questionAnswers()
            ->forInputSource($inputSource)->get();
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
     * Return the question answers for all input sources.
     *
     * @return mixed
     */
    public function questionAnswersForMe()
    {
        // only there for eager loading, user in the App\Http\ViewComposers\ToolCompsoser
        return $this->questionAnswers()->forMe();
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
