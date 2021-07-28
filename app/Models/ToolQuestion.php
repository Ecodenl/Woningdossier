<?php

namespace App\Models;

use App\Traits\Models\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class ToolQuestion extends Model
{
    use HasTranslations;

    protected $translatable = [
        'name',
        'placeholder',
        'help_text'
    ];

    protected $fillable = [
        'short',
        'conditions',
        'placeholder',
        'name',
        'help_text',
        'tool_question_type_id',
        'save_in',
        'unit_of_measure',
        'options',
        'validation',
        'coach',
        'resident'
    ];

    protected $casts = [
        'conditions' => 'array',
        'options' => 'array',
        'save_in' => 'array',
        'validation' => 'array',
        'coach' => 'boolean',
        'resident' => 'boolean',
    ];

    /**
     * Method to return the intermediary morph table
     *
     * @return HasMany
     */
    public function toolQuestionValuables(): HasMany
    {
        return $this->hasMany(ToolQuestionValuable::class);
    }

    public function toolQuestionCustomValues()
    {
        return $this->hasMany(ToolQuestionCustomValue::class);
    }

    /**
     * Method to return the question values  (morphed models / the options for the question)
     *
     * @return mixed
     */
    public function getQuestionValues(): Collection
    {
        // relationships exists on the toolQuestionValuable model as well.
        return $this
            ->toolQuestionValuables()
            ->visible()
            ->ordered()
            ->with('toolQuestionValuables')
            ->get()
            ->pluck('tool_question_valuable');
    }
}
