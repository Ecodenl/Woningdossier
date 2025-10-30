<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Services\DiscordNotifier;
use App\Traits\HasShortTrait;
use App\Traits\Models\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

/**
 * App\Models\ToolQuestion
 *
 * @property int $id
 * @property string|null $short
 * @property string|null $save_in
 * @property int|null $for_specific_input_source_id
 * @property array<array-key, mixed> $name
 * @property array<array-key, mixed> $help_text
 * @property array<array-key, mixed>|null $placeholder
 * @property string $data_type
 * @property bool $coach
 * @property bool $resident
 * @property array<array-key, mixed>|null $options
 * @property array<array-key, mixed>|null $validation
 * @property string|null $unit_of_measure
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\InputSource|null $forSpecificInputSource
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SubSteppable> $subSteppables
 * @property-read int|null $sub_steppables_count
 * @property-read \App\Models\SubSteppable|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SubStep> $subSteps
 * @property-read int|null $sub_steps_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ToolQuestionAnswer> $toolQuestionAnswers
 * @property-read int|null $tool_question_answers_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ToolQuestionCustomValue> $toolQuestionCustomValues
 * @property-read int|null $tool_question_custom_values_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ToolQuestionValuable> $toolQuestionValuables
 * @property-read int|null $tool_question_valuables_count
 * @property-read mixed $translations
 * @method static \Database\Factories\ToolQuestionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ToolQuestion findByShortsOrdered($shorts)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ToolQuestion newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ToolQuestion newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ToolQuestion query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ToolQuestion whereCoach($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ToolQuestion whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ToolQuestion whereDataType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ToolQuestion whereForSpecificInputSourceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ToolQuestion whereHelpText($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ToolQuestion whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ToolQuestion whereJsonContainsLocale(string $column, string $locale, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ToolQuestion whereJsonContainsLocales(string $column, array $locales, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ToolQuestion whereLocale(string $column, string $locale)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ToolQuestion whereLocales(string $column, array $locales)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ToolQuestion whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ToolQuestion whereOptions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ToolQuestion wherePlaceholder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ToolQuestion whereResident($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ToolQuestion whereSaveIn($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ToolQuestion whereShort($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ToolQuestion whereUnitOfMeasure($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ToolQuestion whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ToolQuestion whereValidation($value)
 * @mixin \Eloquent
 */
class ToolQuestion extends Model
{
    use HasFactory;

    use HasTranslations, HasShortTrait;

    protected $translatable = [
        'name',
        'placeholder',
        'help_text'
    ];

    protected $fillable = [
        'short',
        'placeholder',
        'data_type',
        'name',
        'help_text',
        'save_in',
        'for_specific_input_source_id',
        'unit_of_measure',
        'options',
        'validation',
        'coach',
        'resident'
    ];

    protected function casts(): array
    {
        return [
            'options' => 'array',
            'validation' => 'array',
            'coach' => 'boolean',
            'resident' => 'boolean',
        ];
    }

    # Model methods
    public function hasOptions(): bool
    {
        return ! empty($this->options);
    }

    /**
     * Method to return the question values  (morphed models / the options for the question)
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
                        $questionValue = Arr::only(
                            $valuable->toArray(),
                            ['calculate_value', 'short', 'building_type_id', 'cooperation_id']
                        );
                        $questionValue['extra'] = $toolQuestionValuable->extra;
                        // the humane readable name is either set in the name or value column.
                        $questionValue['name'] = $valuable->name ?? $valuable->value ?? $valuable->measure_name;
                        $questionValue['value'] = $valuable->id;
                        $questionValue['conditions'] = $toolQuestionValuable->conditions;

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
                $questionValue['conditions'] = $toolQuestionCustomValue->conditions;

                return $questionValue;
            });
    }

    # Relations
    public function toolQuestionAnswers(): HasMany
    {
        return $this->hasMany(ToolQuestionAnswer::class);
    }

    public function subSteppables(): MorphMany
    {
        return $this->morphMany(SubSteppable::class, 'sub_steppable');
    }

    public function subSteps(): BelongsToMany
    {
        return $this->morphToMany(SubStep::class, 'sub_steppable')
            ->using(SubSteppable::class)
            ->withPivot('order', 'size', 'conditions', 'tool_question_type_id');
    }

    #[Scope]
    protected function findByShortsOrdered($builder, $shorts)
    {
        $questionMarks = substr(str_repeat('?, ', count($shorts)), 0, -2);

        return $builder
            ->orderByRaw("FIELD(short, {$questionMarks})", $shorts)
            ->whereIn('short', $shorts);
    }

    /**
     * Method to return the intermediary morph table
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
}
