<?php

namespace App\Models;

use App\Traits\HasShortTrait;
use App\Traits\Models\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * App\Models\ToolLabel
 *
 * @property int $id
 * @property array<array-key, mixed> $name
 * @property string $short
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SubSteppable> $subSteppables
 * @property-read int|null $sub_steppables_count
 * @property-read \App\Models\SubSteppable|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SubStep> $subSteps
 * @property-read int|null $sub_steps_count
 * @property-read mixed $translations
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ToolLabel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ToolLabel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ToolLabel query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ToolLabel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ToolLabel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ToolLabel whereJsonContainsLocale(string $column, string $locale, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ToolLabel whereJsonContainsLocales(string $column, array $locales, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ToolLabel whereLocale(string $column, string $locale)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ToolLabel whereLocales(string $column, array $locales)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ToolLabel whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ToolLabel whereShort($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ToolLabel whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ToolLabel extends Model
{
    use HasShortTrait, HasTranslations;

    protected $translatable = [
        'name',
        'slug',
    ];

    # Relations
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
}
