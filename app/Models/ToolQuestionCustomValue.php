<?php

namespace App\Models;

use App\Traits\HasShortTrait;
use App\Traits\Models\HasTranslations;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

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
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read array $translations
 * @method static Builder|ToolQuestionCustomValue newModelQuery()
 * @method static Builder|ToolQuestionCustomValue newQuery()
 * @method static Builder|ToolQuestionCustomValue ordered()
 * @method static Builder|ToolQuestionCustomValue query()
 * @method static Builder|ToolQuestionCustomValue visible()
 * @method static Builder|ToolQuestionCustomValue whereCreatedAt($value)
 * @method static Builder|ToolQuestionCustomValue whereExtra($value)
 * @method static Builder|ToolQuestionCustomValue whereId($value)
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
    use HasTranslations, HasShortTrait;

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

    public function scopeVisible(Builder $query): Builder
    {
        return $query->where('show', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('order');
    }
}
