<?php

namespace App\Models;

use App\Models\ToolQuestionAnswer;
use App\Traits\HasShortTrait;
use App\Traits\Models\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use PhpParser\Node\Expr\AssignOp\Mod;

class ToolQuestion extends Model
{
    use HasTranslations, HasShortTrait;

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
        'for_specific_input_source_id',
        'unit_of_measure',
        'options',
        'validation',
        'coach',
        'resident'
    ];

    protected $casts = [
        'conditions' => 'array',
        'options' => 'array',
        'validation' => 'array',
        'coach' => 'boolean',
        'resident' => 'boolean',
    ];

    public function hasOptions(): bool
    {
        return  !empty($this->options);
    }

    public function toolQuestionType(): BelongsTo
    {
        return $this->belongsTo(ToolQuestionType::class);
    }

    public function toolQuestionAnswers(): HasMany
    {
        return $this->hasMany(ToolQuestionAnswer::class);
    }

    public function subSteps(): BelongsToMany
    {
        return $this->belongsToMany(SubStep::class, 'sub_step_tool_questions');
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

    public function forSpecificInputSource(): BelongsTo
    {
        return $this->belongsTo(InputSource::class);
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
                ->map(function (ToolQuestionValuable $toolQuestionValuable) {
                    // so now get the actual morphed model.
                    $valuable = $toolQuestionValuable->tool_question_valuable;

                    $questionValue = Arr::only($valuable->toArray(), ['calculate_value', 'short']);
                    $questionValue['extra'] = $toolQuestionValuable->extra;
                    // the humane readable name is either set in the name or value column.
                    $questionValue['name'] = $valuable->name ?? $valuable->value;
                    $questionValue['value'] = $valuable->id;

                    return $questionValue;
                });
        }
        return $this->toolQuestionCustomValues()
            ->visible()
            ->ordered()
            ->get()
            ->map(function ($toolQuestionCustomValue) {
                $questionValue = $toolQuestionCustomValue->toArray();
                $questionValue['name'] = $toolQuestionCustomValue->name;
                $questionValue['value'] = $toolQuestionCustomValue->short;

                return $questionValue;
            });


    }
}
