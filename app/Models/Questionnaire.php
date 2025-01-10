<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\HasCooperationTrait;
use App\Traits\Models\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\Questionnaire
 *
 * @property int $id
 * @property array<array-key, mixed> $name
 * @property int|null $step_id
 * @property int $cooperation_id
 * @property int $order
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Cooperation $cooperation
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\QuestionnaireStep> $questionnaireSteps
 * @property-read int|null $questionnaire_steps_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Question> $questions
 * @property-read int|null $questions_count
 * @property-read \App\Models\QuestionnaireStep|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Step> $steps
 * @property-read int|null $steps_count
 * @property-read mixed $translations
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Questionnaire active()
 * @method static \Database\Factories\QuestionnaireFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Questionnaire forAllCooperations()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Questionnaire forMyCooperation($cooperationId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Questionnaire newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Questionnaire newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Questionnaire query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Questionnaire whereCooperationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Questionnaire whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Questionnaire whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Questionnaire whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Questionnaire whereJsonContainsLocale(string $column, string $locale, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Questionnaire whereJsonContainsLocales(string $column, array $locales, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Questionnaire whereLocale(string $column, string $locale)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Questionnaire whereLocales(string $column, array $locales)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Questionnaire whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Questionnaire whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Questionnaire whereStepId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Questionnaire whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Questionnaire extends Model
{
    use HasFactory,
        HasCooperationTrait,
        HasTranslations;

    protected $translatable = [
        'name',
    ];

    protected $fillable = [
        'name', 'step_id', 'cooperation_id', 'is_active', 'order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'bool',
        ];
    }

    /**
     * Return the step that belongs to this questionnaire.
     */
    public function steps(): BelongsToMany
    {
        return $this->belongsToMany(Step::class)
            ->using(QuestionnaireStep::class)
            ->withPivot('order');
    }

    public function questionnaireSteps(): HasMany
    {
        return $this->hasMany(QuestionnaireStep::class);
    }

    /**
     * Return the cooperation that belongs to this questionnaire.
     */
    public function cooperation(): BelongsTo
    {
        return $this->belongsTo(Cooperation::class);
    }

    /**
     * Check if the questionnaire is active.
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    public function isNotActive()
    {
        return ! $this->isActive();
    }

    /**
     * Return all the questions from the questionnaire.
     */
    public function questions(): HasMany
    {
        return $this->hasMany(Question::class)->orderBy('order');
    }

    /**
     * Scope the active questionnaires.
     *
     * @param $query
     *
     * @return mixed
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
