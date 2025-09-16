<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
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
 * @property array<array-key, mixed> $name
 * @property bool $show
 * @property int $order
 * @property array<array-key, mixed>|null $extra
 * @property array<array-key, mixed>|null $conditions
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\ToolQuestion $toolQuestion
 * @property-read mixed $translations
 * @method static \Database\Factories\ToolQuestionCustomValueFactory factory($count = null, $state = [])
 * @method static Builder<static>|ToolQuestionCustomValue newModelQuery()
 * @method static Builder<static>|ToolQuestionCustomValue newQuery()
 * @method static Builder<static>|ToolQuestionCustomValue ordered(string $direction = 'asc')
 * @method static Builder<static>|ToolQuestionCustomValue query()
 * @method static Builder<static>|ToolQuestionCustomValue whereConditions($value)
 * @method static Builder<static>|ToolQuestionCustomValue whereCreatedAt($value)
 * @method static Builder<static>|ToolQuestionCustomValue whereExtra($value)
 * @method static Builder<static>|ToolQuestionCustomValue whereId($value)
 * @method static Builder<static>|ToolQuestionCustomValue whereJsonContainsLocale(string $column, string $locale, ?mixed $value, string $operand = '=')
 * @method static Builder<static>|ToolQuestionCustomValue whereJsonContainsLocales(string $column, array $locales, ?mixed $value, string $operand = '=')
 * @method static Builder<static>|ToolQuestionCustomValue whereLocale(string $column, string $locale)
 * @method static Builder<static>|ToolQuestionCustomValue whereLocales(string $column, array $locales)
 * @method static Builder<static>|ToolQuestionCustomValue whereName($value)
 * @method static Builder<static>|ToolQuestionCustomValue whereOrder($value)
 * @method static Builder<static>|ToolQuestionCustomValue whereShort($value)
 * @method static Builder<static>|ToolQuestionCustomValue whereShow($value)
 * @method static Builder<static>|ToolQuestionCustomValue whereToolQuestionId($value)
 * @method static Builder<static>|ToolQuestionCustomValue whereUpdatedAt($value)
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

    protected function casts(): array
    {
        return [
            'show' => 'boolean',
            'extra' => 'array',
            'conditions' => 'array',
        ];
    }

    # Scopes
    #[Scope]
    protected function visible(Builder $query): Builder
    {
        return $query->where('show', true);
    }

    # Relations
    public function toolQuestion(): BelongsTo
    {
        return $this->belongsTo(ToolQuestion::class);
    }
}
