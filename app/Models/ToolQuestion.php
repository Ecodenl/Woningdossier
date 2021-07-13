<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class ToolQuestion extends Model
{
    protected $fillable = [
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
        'name' => 'array',
        'help_text' => 'array',
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
    public function toolQuestionValueables(): HasMany
    {
        return $this->hasMany(ToolQuestionValueable::class);
    }

    /**
     * Method to return the question values (the options for the question)
     *
     * @return mixed
     */
    public function getQuestionValues(): Collection
    {
        // relationships exists on the toolQuestionValueable model as well.
        return $this
            ->toolQuestionValueables()
            ->visible()
            ->ordered()
            ->with('toolQuestionValueables')
            ->get()
            ->pluck('tool_question_valueable');
    }
}
