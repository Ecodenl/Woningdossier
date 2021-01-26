<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;

/**
 * App\Models\LanguageLine.
 *
 * @property int                                                     $id
 * @property string                                                  $group
 * @property string                                                  $key
 * @property array                                                   $text
 * @property int|null                                                $step_id
 * @property int|null                                                $main_language_line_id
 * @property int|null                                                $help_language_line_id
 * @property \Illuminate\Support\Carbon|null                         $created_at
 * @property \Illuminate\Support\Carbon|null                         $updated_at
 * @property LanguageLine|null                                       $helpText
 * @property \Illuminate\Database\Eloquent\Collection|LanguageLine[] $subQuestions
 * @property int|null                                                $sub_questions_count
 *
 * @method static Builder|LanguageLine forGroup($group)
 * @method static Builder|LanguageLine mainQuestions()
 * @method static Builder|LanguageLine newModelQuery()
 * @method static Builder|LanguageLine newQuery()
 * @method static Builder|LanguageLine query()
 * @method static Builder|LanguageLine whereCreatedAt($value)
 * @method static Builder|LanguageLine whereGroup($value)
 * @method static Builder|LanguageLine whereHelpLanguageLineId($value)
 * @method static Builder|LanguageLine whereId($value)
 * @method static Builder|LanguageLine whereKey($value)
 * @method static Builder|LanguageLine whereMainLanguageLineId($value)
 * @method static Builder|LanguageLine whereStepId($value)
 * @method static Builder|LanguageLine whereText($value)
 * @method static Builder|LanguageLine whereUpdatedAt($value)
 * @mixin \Eloquent
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

    public function scopeForGroup(Builder $query, $group)
    {
        return $query->where('group', $group);
    }

    /**
     * Scope a query to only return the main questions.
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
     * Check if a language line is a helptext.
     *
     * It is considered to be a helptext is the help_language_line_id is null
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
     */
    public function isNotHelpText(): bool
    {
        return ! $this->isHelpText();
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
