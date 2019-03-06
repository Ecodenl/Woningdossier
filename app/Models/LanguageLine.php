<?php

namespace App\Models;

/**
 * Although this is a model to retrieve languages, we use it to store "questions" which are ofcourse just translations.
 *
 * Most of the methods found in the model will be used to treat the language_lines as questions, main questions, subquestions and helptexts.
 *
 * Class LanguageLine
 * @package App\Models
 */
class LanguageLine extends \Spatie\TranslationLoader\LanguageLine
{
    protected $fillable = [
        'group', 'key', 'text', 'step_id', 'main_language_line_id', 'help_language_line_id',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (static::where('group', $model->group)->where('key', $model->key)->first() instanceof LanguageLine) {
                \Log::debug('duplicate key: '.$model->key);
            }
        });
    }

    /**
     * Scope a query to only return the main questions
     *
     * @param $query
     *
     * @return mixed
     */
    public function scopeMainQuestions($query)
    {
        return $query->whereNull('main_language_line_id');
    }

    /**
     * Check if a language line is a helptext
     *
     * It is considered to be a helptext is the help_language_line_id is null
     *
     * @return bool
     */
    public function isHelpText(): bool
    {
        // try to obtain the question from the helptext
        $questionFromHelpText = LanguageLine::where('help_language_line_id', $this->id)->first();

        // if the language line has no help_language_line_id and the $this->id is found in a language_line
        // where help_language_line_id = $this->id, we know its a helptext
        if (is_null($this->help_language_line_id) && $questionFromHelpText instanceof LanguageLine) {
            return true;
        }
        return false;
    }

    /**
     * @see isHelpText()
     *
     * @return bool
     */
    public function isNotHelpText(): bool
    {
        return !$this->isHelpText();
    }

    /**
     * Get the sub questions.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subQuestions()
    {
        return $this->hasMany(self::class, 'main_language_line_id', 'id');
    }

    public function helpText()
    {
        return $this->hasOne(self::class, 'id', 'help_language_line_id');
    }
}
