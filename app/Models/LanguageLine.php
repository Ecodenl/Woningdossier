<?php

namespace App\Models;

class LanguageLine extends \Spatie\TranslationLoader\LanguageLine
{
    protected $fillable = [
        'group', 'key', 'text', 'step_id', 'main_language_line_id', 'help_language_line_id'
    ];

    public static function boot()
    {
        static::creating(function ($model) {
            if (static::where('group', $model->group)->where('key', $model->key)->first() instanceof LanguageLine) {
                \Log::debug('duplicate key: '.$model->key);
            }
        });
    }

    /**
     * Scope a query to only return the main questions and exclude the helptexts
     *
     * @param $query
     * @return mixed
     */
    public function scopeMainQuestions($query)
    {
        return $query
            ->where('main_language_line_id', null)
            ->where('help_language_line_id', '!=', null);
    }

    /**
     * Get the sub questions.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subQuestions()
    {
        return $this->hasMany(LanguageLine::class, 'main_language_line_id', 'id');
    }

    public function helpTexts()
    {
        return $this->hasMany(LanguageLine::class, 'help_language_line_id', 'id');
    }
}
