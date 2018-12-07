<?php

namespace App\Models;

use App\Helpers\TranslatableTrait;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use TranslatableTrait;

    protected $fillable = [
        'name', 'type', 'order', 'required', 'questionnaire_id'
    ];

    protected $casts = [
        'required' => 'bool'
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
     * Return the options from a questions, a question will have options if its a radio, checkbox or dropdown etc.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function questionOptions()
    {
        return $this->hasMany(QuestionOption::class);
    }
}
