<?php

namespace App\Models;

use App\Services\DiscordNotifier;
use App\Traits\HasShortTrait;
use App\Traits\Models\HasTranslations;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

/**
 * App\Models\ToolQuestion
 *
 * @property int $id
 * @property string|null $short
 * @property string|null $save_in
 * @property int|null $for_specific_input_source_id
 * @property array|null $conditions
 * @property array $name
 * @property array $help_text
 * @property array|null $placeholder
 * @property int $tool_question_type_id
 * @property bool $coach
 * @property bool $resident
 * @property array|null $options
 * @property array|null $validation
 * @property string|null $unit_of_measure
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\InputSource|null $forSpecificInputSource
 * @property-read array $translations
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\SubStep[] $subSteps
 * @property-read int|null $sub_steps_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ToolQuestionAnswer[] $toolQuestionAnswers
 * @property-read int|null $tool_question_answers_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ToolQuestionCustomValue[] $toolQuestionCustomValues
 * @property-read int|null $tool_question_custom_values_count
 * @property-read \App\Models\ToolQuestionType $toolQuestionType
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ToolQuestionValuable[] $toolQuestionValuables
 * @property-read int|null $tool_question_valuables_count
 * @method static \Illuminate\Database\Eloquent\Builder|ToolQuestion newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ToolQuestion newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ToolQuestion query()
 * @method static \Illuminate\Database\Eloquent\Builder|ToolQuestion whereCoach($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ToolQuestion whereConditions($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ToolQuestion whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ToolQuestion whereForSpecificInputSourceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ToolQuestion whereHelpText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ToolQuestion whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ToolQuestion whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ToolQuestion whereOptions($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ToolQuestion wherePlaceholder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ToolQuestion whereResident($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ToolQuestion whereSaveIn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ToolQuestion whereShort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ToolQuestion whereToolQuestionTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ToolQuestion whereUnitOfMeasure($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ToolQuestion whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ToolQuestion whereValidation($value)
 * @mixin \Eloquent
 */
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

    # Model Methods
    /**
     * Find a tool question and immediately set its question type.
     *
     * @param  int  $id
     * @param  \App\Models\SubStep  $subStep
     *
     * @return \App\Models\ToolQuestion|null
     */
    public static function findWithType(int $id, SubStep $subStep): ?ToolQuestion
    {
        $toolQuestion = static::find($id);
        if ($toolQuestion instanceof ToolQuestion) {
            $toolQuestion->setToolQuestionType($subStep);
        }
        return $toolQuestion;
    }

    /**
     * Set the tool question type for the tool question.
     *
     * @param  \App\Models\SubStep  $subStep
     *
     * @return void
     */
    public function setToolQuestionType(SubStep $subStep)
    {
        $this->toolQuestionType = $this->getToolQuestionTypeForSubStep($subStep);
    }

    public function hasOptions(): bool
    {
        return ! empty($this->options);
    }

    /**
     * Each sub step only has a single tool question type per tool question. This retrieves that specific type.
     *
     * @param  \App\Models\SubStep  $subStep
     *
     * @return \App\Models\ToolQuestionType|null
     */
    public function getToolQuestionTypeForSubStep(SubStep $subStep): ?ToolQuestionType
    {
        return $this->toolQuestionTypes()->wherePivot('sub_step_id', $subStep->id)->first();
    }

    # Attributes
    public function getToolQuestionTypeAttribute($value)
    {
        // We hope the developer has properly eager loaded the value and so we can use magic to get it
        if (is_null($value) && $this->toolQuestionTypes->count() === 1) {
            $value = $this->toolQuestionTypes->first();
        }

        return $value;
    }

    # Scopes
    public function scopeWithToolQuestionType(Builder $query, SubStep $subStep): Builder
    {
        if (is_null($subStep->id)) {
            throw new \Exception('Can\'t eager load a tool question type without sub step!');
        }

        return $query->with(['toolQuestionTypes' => function ($query) use ($subStep) {
            $query->wherePivot('sub_step_id', $subStep->id);
        }]);
    }

    # Relations
    public function toolQuestionTypes(): BelongsToMany
    {
        return $this->belongsToMany(ToolQuestionType::class, 'sub_step_tool_questions')
            ->withPivot('sub_step_id');
    }

    public function toolQuestionAnswers(): HasMany
    {
        return $this->hasMany(ToolQuestionAnswer::class);
    }

    public function subSteps(): BelongsToMany
    {
        return $this->belongsToMany(SubStep::class, 'sub_step_tool_questions')
            ->withPivot('tool_question_type_id');
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

    public function toolQuestionCustomValues(): HasMany
    {
        return $this->hasMany(ToolQuestionCustomValue::class);
    }

    public function forSpecificInputSource(): BelongsTo
    {
        return $this->belongsTo(InputSource::class);
    }

    # Unsorted
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
                    if ($valuable instanceof Model) {

                        // these will also be available in the frontend, to the user.
                        // be careful choosing what you allow.
                        $questionValue = Arr::only($valuable->toArray(), ['calculate_value', 'short', 'building_type_id', 'cooperation_id']);
                        $questionValue['extra'] = $toolQuestionValuable->extra;
                        // the humane readable name is either set in the name or value column.
                        $questionValue['name'] = $valuable->name ?? $valuable->value;
                        $questionValue['value'] = $valuable->id;

                        return $questionValue;
                    } else {
                        (new DiscordNotifier())->notify("<@!184734207413583872>, <@!363259746859483136>: ToolQuestionValuable {$toolQuestionValuable->id} has a non-existing valuable!");
                    }

                    return null;
                })
                ->filter(function ($value) {
                    return ! is_null($value);
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
