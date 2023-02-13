<?php

namespace App\Models;

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
 * @property array $name
 * @property int $cooperation_id
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Cooperation $cooperation
 * @property-read array $translations
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\QuestionnaireStep[] $questionnaireSteps
 * @property-read int|null $questionnaire_steps_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Question[] $questions
 * @property-read int|null $questions_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Step[] $steps
 * @property-read int|null $steps_count
 * @method static \Illuminate\Database\Eloquent\Builder|Questionnaire active()
 * @method static \Database\Factories\QuestionnaireFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Questionnaire forAllCooperations()
 * @method static \Illuminate\Database\Eloquent\Builder|Questionnaire forMyCooperation($cooperationId)
 * @method static \Illuminate\Database\Eloquent\Builder|Questionnaire newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Questionnaire newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Questionnaire query()
 * @method static \Illuminate\Database\Eloquent\Builder|Questionnaire whereCooperationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Questionnaire whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Questionnaire whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Questionnaire whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Questionnaire whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Questionnaire whereUpdatedAt($value)
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

    protected $casts = [
        'is_active' => 'bool',
    ];

    /**
     * Return the step that belongs to this questionnaire.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
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
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cooperation()
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
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function questions()
    {
        return $this->hasMany(Question::class);
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
