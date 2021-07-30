<?php

namespace App\Models;

use App\ToolQuestionAnswer;
use App\Traits\Models\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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


    public function toolQuestionType(): BelongsTo
    {
        return $this->belongsTo(ToolQuestionType::class);
    }

    public function toolQuestionAnswers(): HasMany
    {
        return $this->hasMany(ToolQuestionAnswer::class);
    }
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
        if ($this->toolQuestionValuables()->exists()) {
            return $this->toolQuestionValuables()
                ->visible()
                ->ordered()
                ->with('toolQuestionValuables')
                ->get()
                ->map(function ($toolQuestion) {
                    $toolQuestionValuable = $toolQuestion->tool_question_valuable;
                    $questionValue = $toolQuestionValuable->toArray();

                    $questionValue['extra'] = $toolQuestion->extra;
                    $questionValue['name'] = $toolQuestionValuable->name ?? $toolQuestionValuable->value;

                    return $questionValue;
                });


        }
        return $this->toolQuestionCustomValues()
            ->visible()
            ->ordered()
            ->get()
            ->map(function ($toolQuestion) {
                $questionValue = $toolQuestion->toArray();
                $questionValue['name'] = $toolQuestion->name;

                return $questionValue;
            });
    }
}
