<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Helpers\HoomdossierSession;
use App\Traits\Models\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Question
 *
 * @property int $id
 * @property array<array-key, mixed> $name
 * @property string $type
 * @property int $order
 * @property bool $required
 * @property array<array-key, mixed>|null $validation
 * @property int $questionnaire_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\TFactory|null $use_factory
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\QuestionsAnswer> $questionAnswers
 * @property-read int|null $question_answers_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\QuestionOption> $questionOptions
 * @property-read int|null $question_options_count
 * @property-read \App\Models\Questionnaire $questionnaire
 * @property-read mixed $translations
 * @method static \Database\Factories\QuestionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereJsonContainsLocale(string $column, string $locale, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereJsonContainsLocales(string $column, array $locales, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereLocale(string $column, string $locale)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereLocales(string $column, array $locales)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereQuestionnaireId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereRequired($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereValidation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question withoutTrashed()
 * @mixin \Eloquent
 */
class Question extends Model
{
    use HasFactory,
        SoftDeletes,
        HasTranslations;

    protected $translatable = [
        'name',
    ];

    protected $fillable = [
        'name', 'type', 'order', 'required', 'questionnaire_id', 'validation',
    ];

    protected function casts(): array
    {
        return [
            'required' => 'bool',
            'validation' => 'array',
        ];
    }

    /**
     * Check if a question is required.
     */
    public function isRequired(): bool
    {
        return $this->required;
    }

    /**
     * Check if a question has validation.
     */
    public function hasValidation(): bool
    {
        return is_array($this->validation) && ! empty($this->validation);
    }

    /**
     * Check if a question has no validation.
     */
    public function hasNoValidation(): bool
    {
        return ! $this->hasValidation();
    }

    /**
     * Return the options from a questions, a question will have options if its a radio, checkbox or dropdown etc.
     */
    public function questionOptions(): HasMany
    {
        return $this->hasMany(QuestionOption::class);
    }

    /**
     * Check if a question has a question option.
     */
    public function hasQuestionOptions(): bool
    {
        return $this->questionOptions()->exists();
    }

    /**
     * Return all the answers for a question.
     */
    public function questionAnswers(): HasMany
    {
        // If you're wondering why this is resulting in specific answers, or maybe you want a specific answer, but
        // you're getting a ton of answers, check view composers to see if these might be set to only be for
        // the current building
        return $this->hasMany(QuestionsAnswer::class);
    }

    /**
     * Return the question answers for all input sources.
     */
    public function questionAnswersForMe(): HasMany
    {
        // only there for eager loading, user in the App\Http\ViewComposers\ToolComposer
        return $this->questionAnswers()->forMe();
    }

    /**
     * Get the questionnaire from a question.
     */
    public function questionnaire(): BelongsTo
    {
        return $this->belongsTo(Questionnaire::class);
    }
}
