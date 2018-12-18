<?php

namespace App\Models;

use App\Helpers\HoomdossierSession;
use App\Helpers\TranslatableTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Question extends Model
{
    use TranslatableTrait, SoftDeletes;

    protected $fillable = [
        'name', 'type', 'order', 'required', 'questionnaire_id', 'validation'
    ];

    protected $dates = [
        'deleted_at'
    ];
    protected $casts = [
        'required' => 'bool',
        'validation' => 'array'
    ];

    /**
     * Check if a question is required
     *
     * @return bool
     */
    public function isRequired() : bool
    {
        if ($this->required == true) {
            return true;
        }

        return false;
    }

    /**
     * Check if a question has validation
     *
     * @return bool
     */
    public function hasValidation() : bool
    {
        if (is_array($this->validation) && !empty($this->validation)) {
            return true;
        }

        return false;
    }
    /**
     * Check if a question has validation
     *
     * @return bool
     */
    public function hasNoValidation() : bool
    {
        return !$this->hasValidation();
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
     * Check if a question has a question option
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
     * Return all the answers for a question
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function questionAnswers()
    {
        return $this->hasMany(QuestionsAnswer::class);
    }

    /**
     * Return the answer on a question for a building and input source
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
}
