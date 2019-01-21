<?php

namespace App\Models;

use App\Helpers\TranslatableTrait;
use App\Scopes\CooperationScope;
use Illuminate\Database\Eloquent\Model;

class Questionnaire extends Model
{
    use TranslatableTrait;

    protected $fillable = [
        'name', 'step_id', 'cooperation_id', 'is_active', 'order'
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
     * Scope the active questionnaires
     *
     * @param $query
     * @return mixed
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
