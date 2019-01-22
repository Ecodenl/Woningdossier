<?php

namespace App\Models;

use App\Helpers\TranslatableTrait;
use App\Scopes\CooperationScope;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Questionnaire.
 *
 * @property int $id
 * @property string $name
 * @property int|null $step_id
 * @property int $cooperation_id
 * @property int $order
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \App\Models\Cooperation $cooperation
 * @property \Illuminate\Database\Eloquent\Collection|\App\Models\Question[] $questions
 * @property \App\Models\Step|null $step
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Questionnaire active()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Questionnaire newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Questionnaire newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Questionnaire query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Questionnaire translated($attribute, $name, $locale = 'nl')
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Questionnaire whereCooperationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Questionnaire whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Questionnaire whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Questionnaire whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Questionnaire whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Questionnaire whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Questionnaire whereStepId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Questionnaire whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Questionnaire extends Model
{
    use TranslatableTrait;

    protected $fillable = [
        'name', 'step_id', 'cooperation_id', 'is_active', 'order',
    ];

    protected $casts = [
        'is_active' => 'bool',
    ];

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new CooperationScope());
    }

    /**
     * Return the step that belongs to this questionnaire.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function step()
    {
        return $this->belongsTo(Step::class);
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
     *
     * @return bool
     */
    public function isActive(): bool
    {
        if ($this->is_active) {
            return true;
        }

        return false;
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
