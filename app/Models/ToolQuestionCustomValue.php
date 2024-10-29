<?php

namespace App\Models;

use App\Traits\HasShortTrait;
use App\Traits\Models\HasOrder;
use App\Traits\Models\HasTranslations;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\ToolQuestionCustomValue
 *
 * @property int $id
 * @property int $tool_question_id
 * @property string $short
 * @property array $name
 * @property bool $show
 * @property int $order
 * @property array|null $extra
 * @property array|null $conditions
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\ToolQuestion $toolQuestion
 * @property-read mixed $translations
 * @method static \Database\Factories\ToolQuestionCustomValueFactory factory($count = null, $state = [])
 * @method static Builder|ToolQuestionCustomValue newModelQuery()
 * @method static Builder|ToolQuestionCustomValue newQuery()
 * @method static Builder|ToolQuestionCustomValue ordered(string $direction = 'asc')
 * @method static Builder|ToolQuestionCustomValue query()
 * @method static Builder|ToolQuestionCustomValue visible()
 * @method static Builder|ToolQuestionCustomValue whereConditions($value)
 * @method static Builder|ToolQuestionCustomValue whereCreatedAt($value)
 * @method static Builder|ToolQuestionCustomValue whereExtra($value)
 * @method static Builder|ToolQuestionCustomValue whereId($value)
 * @method static Builder|ToolQuestionCustomValue whereJsonContainsLocale(string $column, string $locale, ?mixed $value, string $operand = '=')
 * @method static Builder|ToolQuestionCustomValue whereJsonContainsLocales(string $column, array $locales, ?mixed $value, string $operand = '=')
 * @method static Builder|ToolQuestionCustomValue whereLocale(string $column, string $locale)
 * @method static Builder|ToolQuestionCustomValue whereLocales(string $column, array $locales)
 * @method static Builder|ToolQuestionCustomValue whereName($value)
 * @method static Builder|ToolQuestionCustomValue whereOrder($value)
 * @method static Builder|ToolQuestionCustomValue whereShort($value)
 * @method static Builder|ToolQuestionCustomValue whereShow($value)
 * @method static Builder|ToolQuestionCustomValue whereToolQuestionId($value)
 * @method static Builder|ToolQuestionCustomValue whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ToolQuestionCustomValue extends Model
{
    use HasFactory,
        HasTranslations,
        HasShortTrait,
        HasOrder;

    protected $translatable = [
        'name'
    ];

    protected $fillable = [
        'name',
        'short',
        'order',
        'validation',
        'options',
        'tool_question_id',
        'show',
        'extra',
    ];

    protected $casts = [
        'show' => 'boolean',
        'extra' => 'array',
        'conditions' => 'array',
    ];

    # Scopes
    public function scopeVisible(Builder $query): Builder
    {
        return $query->where('show', true);
    }

    # Relations
    public function toolQuestion(): BelongsTo
    {
        return $this->belongsTo(ToolQuestion::class);
    }
}
